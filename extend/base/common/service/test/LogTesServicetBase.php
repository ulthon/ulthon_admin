<?php

namespace base\common\service\test;

use app\common\interface\test\CommandTestInterface;
use app\common\service\TestService;
use think\console\Input;
use think\console\Output;
use think\console\Table;
use think\facade\Log;

class LogTesServicetBase extends TestService implements CommandTestInterface
{
    public const NAME = 'log';

    public const DESC = '测试mysqllog驱动的兼容性、性能、边界情况';

    public const RUN = 'run';

    /**
     * @var Output
     */
    protected $output;

    /**
     * @var Input
     */
    protected $input;

    protected $summary = [];

    protected $configContent = '';

    public function run()
    {
        $this->getLogInfo();
        $this->output->writeln(str_repeat('=', 50));
        $this->testLogContent();
        $this->output->writeln(str_repeat('=', 50));
        $this->testIoTimes();
        $this->output->writeln(str_repeat('=', 50));
        $this->testIoSize();
        $this->output->writeln(str_repeat('=', 50));
        $this->testNetworkChange();
        $this->output->writeln(str_repeat('=', 50));

        $this->output->writeln($this->configContent);

        $this->output->writeln('测试结果汇总');


        $output_table = new Table();

        $output_table->setHeader(['测试项', '测试描述', '测试结果']);

        $output_table->setRows($this->summary);

        $table_content = $output_table->render();

        $this->output->writeln($table_content);
    }

    protected function getLogInfo()
    {
        $this->output->writeln('当前日志配置项');

        $config = Log::getConfig();

        $table = array_to_table($config);

        $output_table = new Table();

        $output_table->setHeader(['配置项', '配置值']);

        $output_table->setRows($table);

        $table_content = $output_table->render();

        $this->configContent = $table_content;

        $this->output->writeln($table_content);

        $this->output->writeln('当前日志驱动：' . $config['default']);
    }

    public function testIoTimes()
    {
        $this->output->writeln('测试写入100条记录，统计写入时间');

        $start_time = microtime(true);

        $total_times = 100;
        for ($i = 0; $i < $total_times; $i++) {
            $log_content = date('Y-m-d H:i:s');

            Log::record($log_content, 'info');

            $this->output->writeln("({$i}/{$total_times})写入日志：{$log_content}");
        }

        $end_time = microtime(true);

        $time = $end_time - $start_time;

        $this->output->writeln('测试完成');

        $this->output->writeln("总写入时间：{$time}秒");

        $this->summary[] = [
            'title' => '写入次数测试',
            'desc' => '测试写入100条记录，统计写入时间',
            'result' => "总写入时间：{$time}秒",
        ];
    }

    public function testIoSize()
    {
        $this->output->writeln('测试大内容写入，统计写入时间和写入大小');

        $this->output->writeln('程序会写入100条记录，间隔1秒，日志内容为100KB的字符串');

        $num = 0;
        $total = 100;

        $log_content = str_repeat('test', 100 * 1000);

        $total_size = 0;
        $total_time = 0;

        while ($num < $total) {
            $num++;

            $start_time = microtime(true);

            Log::record($log_content, 'info');

            $end_time = microtime(true);

            $time = $end_time - $start_time;

            $total_time += $time;

            $size = strlen($log_content);

            $total_size += $size;

            $size = format_bytes($size);
            $this->output->writeln("({$num}/{$total})写入大小：{$size}，写入时间：{$time}秒");
        }

        $this->output->writeln('测试完成');

        $total_size = format_bytes($total_size);
        $this->output->writeln("总写入大小：{$total_size}，总写入时间：{$total_time}秒");

        $this->summary[] = [
            'title' => '写入大小测试',
            'desc' => '测试大内容写入，统计写入时间和写入大小',
            'result' => "总写入大小：{$total_size}，总写入时间：{$total_time}秒",
        ];
    }

    public function testNetworkChange()
    {
        $this->output->writeln('测试网络切换');

        $this->output->writeln('程序会写入30条记录，间隔1秒，日志内容为当前时间，测试期间可以反复断开网络连接，查看是否丢失或报错');

        $num = 0;
        $total = 30;

        while ($num < $total) {
            $num++;

            $log_content = date('Y-m-d H:i:s');

            Log::record($log_content, 'info');

            $this->output->writeln("({$num}/{$total})写入日志：{$log_content}");

            sleep(1);
        }

        $this->output->writeln('测试完成');

        $this->summary[] = [
            'title' => '网络切换测试',
            'desc' => '测试网络切换',
            'result' => '请查看是否有丢失',
        ];
    }

    public function testLogContent()
    {
        $this->output->writeln('测试日志内容兼容性');

        $test_content_item = [];

        // 生成测试内容，包括简单英文字符串、中文字符串、大文本、数字、大数字、负数、负数大数字、数组、对象、资源、布尔值、null、空字符串、空数组、空对象、空资源、空布尔值
        $test_content_item[] = [
            'name' => '简单字符串',
            'content' => 'test',
        ];
        $test_content_item[] = [
            'name' => '中文字符串',
            'content' => '测试',
        ];
        $test_content_item[] = [
            'name' => '大文本',
            'content' => str_repeat('test测试', 1000),
        ];
        $test_content_item[] = [
            'name' => '数字',
            'content' => 1,
        ];
        $test_content_item[] = [
            'name' => '大数字',
            'content' => 100000 * 100000,
        ];
        $test_content_item[] = [
            'name' => '负数',
            'content' => -1,
        ];
        $test_content_item[] = [
            'name' => '负数大数字',
            'content' => -100000 * 100000,
        ];
        $test_content_item[] = [
            'name' => '数组',
            'content' => [
                'test' => 'test',
                '测试' => '测试',
            ],
        ];
        $test_content_item[] = [
            'name' => '对象',
            'content' => new \stdClass(),
        ];
        $test_content_item[] = [
            'name' => '资源',
            'content' => fopen(__FILE__, 'r'),
        ];
        $test_content_item[] = [
            'name' => '布尔值',
            'content' => true,
        ];
        $test_content_item[] = [
            'name' => 'null',
            'content' => null,
        ];
        $test_content_item[] = [
            'name' => '空字符串',
            'content' => '',
        ];
        $test_content_item[] = [
            'name' => '空数组',
            'content' => [],
        ];
        $test_content_item[] = [
            'name' => '空对象',
            'content' => new \stdClass(),
        ];
        $test_content_item[] = [
            'name' => '空资源',
            'content' => fopen('php://memory', 'r'),
        ];
        $test_content_item[] = [
            'name' => '空布尔值',
            'content' => false,
        ];
        $test_content_item[] = [
            'name' => '异常',
            'content' => new \Exception('test'),
        ];

        $fail_count = 0;

        foreach ($test_content_item as $key => $value) {
            try {
                $total_count = count($test_content_item);
                $num = $key + 1;

                Log::record($value['content'], 'info');
                $this->output->writeln("({$num}/{$total_count})" . '测试内容：' . $value['name'] . '，测试结果：成功');
            } catch (\Throwable $th) {
                $this->output->writeln('测试内容失败：' . $value['name'] . '，测试结果：' . $th->getMessage());

                if ($this->output->isDebug()) {
                    $this->output->error($th);
                    break;
                }

                $fail_count++;
            }
        }

        $this->output->writeln('测试完成');

        $this->summary[] = [
            'title' => '日志内容兼容性测试',
            'desc' => '测试日志内容兼容性',
            'result' => "测试完成，失败{$fail_count}个",
        ];
    }

    public function setOutput(Output $output)
    {
        $this->output = $output;
    }

    public function setInput(Input $input)
    {
        $this->input = $input;
    }
}

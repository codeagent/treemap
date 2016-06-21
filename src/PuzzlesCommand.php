<?php
namespace codeagent\treemap\site;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use codeagent\treemap\Gradient;

class PuzzlesCommand extends Command
{
    public static $path = __DIR__ . '/../data/puzzle.php';

    public static $count = 512;

    public static $range = 25;

    /**
     * @var Gradient
     */
    public $gradient;

    public function configure()
    {
        $this->gradient = new Gradient([
            "0.0" => "#0143A3",
//            "0.5" => "#0d538c",
            "1.0" => "#0273D4"
        ]);

        $this
            ->setName('generate:puzzles')
            ->setDescription('Copy treemap assets into the target dir')
            ->addOption("count", "c", InputOption::VALUE_REQUIRED, "Count of particles", static::$count)
            ->addOption("range", "r", InputOption::VALUE_REQUIRED, "Range of calues", static::$range);
    }


    public function execute(InputInterface $input, OutputInterface $output)
    {
        $count = $input->getOption("count");
        $range = $input->getOption("range");

        $min = 10;
        $max = $min + $range;

        $result = [];
        while($count-- > 0) {
            $value    = rand($min, $max);
            $color    = $this->gradient->color(rand() / getrandmax());
            $result[] = ["value" => $value, "color" => $color];
        }

        file_put_contents(static::$path, "<?php return " . var_export($result, true) . "; ?>");
        $output->writeln("Writed to " . realpath(static::$path));
    }

}
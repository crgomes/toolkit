<?php

declare(strict_types=1);

namespace EcEuropa\Toolkit\TaskRunner;

use EcEuropa\Toolkit\Toolkit;
use Robo\Common\ConfigAwareTrait;
use Robo\Contract\ConfigAwareInterface;
use Robo\Exception\TaskException;
use Robo\Robo;
use Robo\Tasks;

/**
 * Class AbstractCommands.
 */
abstract class AbstractCommands extends Tasks implements ConfigAwareInterface
{
    use ConfigAwareTrait;
    use \EcEuropa\Toolkit\Task\File\Tasks;
    use \EcEuropa\Toolkit\Task\Command\Tasks;

    /**
     * Path to YAML configuration file containing command defaults.
     *
     * Command classes should implement this method.
     *
     * @return string
     */
    public function getConfigurationFile()
    {
        return Toolkit::getToolkitRoot() . '/config/base.yml';
    }

    /**
     * Validate and return the path to given bin.
     *
     * @param string $name
     *   The bin to look for.
     *
     * @return string
     *   The bin path.
     *
     * @throws TaskException
     */
    protected function getBin(string $name): string
    {
        $bin = $this->getBinPath($name);
        if (!file_exists($bin) && !$this->isSimulating()) {
            throw new TaskException($this, "Executable '$bin' was not found.");
        }

        return $bin;
    }

    /**
     * Return the path to given bin.
     *
     * @return string
     *   The path to given binary.
     */
    protected function getBinPath(string $name): string
    {
        return $this->getConfigValue('runner.bin_dir') . '/' . $name;
    }

    /**
     * Validate and return the path to given bin from node packages.
     *
     * @param string $name
     *   The bin to look for.
     *
     * @return string
     *   The bin path.
     *
     * @throws TaskException
     */
    protected function getNodeBin(string $name): string
    {
        $bin = $this->getNodeBinPath($name);
        if (!file_exists($bin) && !$this->isSimulating()) {
            throw new TaskException($this, "Executable '$bin' was not found.");
        }

        return $bin;
    }

    /**
     * Return the path to given bin from node packages.
     *
     * @return string
     *   The path to given binary.
     */
    protected function getNodeBinPath(string $name): string
    {
        return $this->getConfigValue('runner.bin_node_dir') . '/' . $name;
    }

    /**
     * Check if current command is being executed with option simulate.
     *
     * @return bool
     *   True if using --simulate, false otherwise.
     */
    protected function isSimulating(): bool
    {
        return (bool) $this->input()->getOption('simulate');
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfigValue($key, $default = null)
    {
        if (!$this->getConfig()) {
            return $default;
        }
        return $this->getConfig()->get($key, $default);
    }

    /**
     * Returns the current working directory.
     *
     * @return string
     *   The current working directory.
     */
    public function getWorkingDir(): string
    {
        return (string) $this->input->getParameterOption('--working-dir', getcwd());
    }

}

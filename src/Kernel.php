<?php

declare(strict_types=1);

namespace App;

use App\General\Application\Compiler\StopwatchCompilerPass;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

/**
 * @package App
 */
class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    protected function build(ContainerBuilder $container): void
    {
        parent::build($container);

        if ($this->environment === 'dev') {
            $container->addCompilerPass(new StopwatchCompilerPass());
        }
    }
}

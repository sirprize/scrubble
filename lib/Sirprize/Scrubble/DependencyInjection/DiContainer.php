<?php

/*
 * This file is part of the Scrubble package
 *
 * (c) Christian Hoegl <chrigu@sirprize.me>
 */

namespace Sirprize\Scrubble\DependencyInjection;

/**
 * DiContainer aliases some Pimple methods.
 *
 * @author Christian Hoegl <chrigu@sirprize.me>
 */
class DiContainer extends \Pimple
{

    public function has($id)
    {
        return parent::offsetExists($id);
    }

    public function get($id)
    {
        try {
            return parent::offsetGet($id);
        }
        catch(\Exception $e) {
            if(extension_loaded('xdebug'))
            {
                throw new DiException(sprintf('"Requesting undefined service "%s" in file "%s" on line %d.', $id, xdebug_call_file(), xdebug_call_line()));
            }
            else {
                throw new DiException(sprintf('Service identifier "%s" is not defined.', $id));
            }
        }
    }

}
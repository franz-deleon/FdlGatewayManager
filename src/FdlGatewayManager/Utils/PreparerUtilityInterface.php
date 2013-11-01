<?php
namespace FdlGatewayManager\Utils;

interface PreparerUtilityInterface
{
    /**
     * What to seed the preparer with
     *
     * @param $seed The seed used by the preparer
     * @return null
     */
    public function seed($seed = null);

    /**
     * Destroys the seed
     *
     * @param  void
     * @return null
     */
    public function destroySeed();
}

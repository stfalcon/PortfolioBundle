<?php
namespace Stfalcon\Bundle\PortfolioBundle\Naming;

use Vich\UploaderBundle\Naming\NamerInterface as NamerInterface;

/**
 * Project Naming
 */
class ProjectNaming implements NamerInterface
{
    /**
     * @param Project $obj
     * @param string $field
     *
     * @return string
     */
    public function name($obj, $field)
    {
        return uniqid() . '.' . $obj->getImageFile()->guessExtension();
    }
}

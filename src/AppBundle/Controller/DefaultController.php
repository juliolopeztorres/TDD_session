<?php

namespace AppBundle\Controller;

use AppBundle\Entity\entityAPI;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    const COORDINATE_X = 2;
    const COORDINATE_Y = 2;
    const RANGE = 1;
    const HEIGHT_WIDTH = 5;

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        $entityAPI = entityAPI::createMatrixForTestWithHeightAndWidth(self::HEIGHT_WIDTH);

        $subMatrix = $entityAPI->getSubMatrixFromCoordinatesAndRange(
            self::COORDINATE_X,
            self::COORDINATE_Y,
            self::RANGE);

        $matrix = $entityAPI->getMatrix();

        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'matrix' => $matrix,
            'custom_width' => 100 / count($matrix[0]),
            'subMatrix' => $subMatrix,
            'range' => self::RANGE,
            'width_matrix' => self::HEIGHT_WIDTH
        ]);
    }
}

<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Matrix;
use AppBundle\Entity\MatrixException;
use AppBundle\Entity\Square;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    const COORDINATE_X = 2;
    const COORDINATE_Y = 2;
    const RANGE = 1;
    const SIDE = 5;

    private $coordinateX;
    private $coordinateY;
    private $range;
    private $side;

    /**
     * @var Request $request
     * @return Response
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $parameters = $this->loadParameters($request->query->all());

        $this->checkParameters();

        $matrix = $this->createMatrix();

        $subMatrix = $matrix->getSubMatrixFromCoordinatesAndRange(
            $this->coordinateX,
            $this->coordinateY,
            $this->range);

        [$matrixTwig, $subMatrixTwig] = $this->prepareForTwig($matrix, $subMatrix);

        return $this->render('default/index.html.twig', [
            'matrix' => $matrixTwig,
            'subMatrix' => $subMatrixTwig,
            'range' => $this->range,
            'width_matrix' => $this->side,
            'parameters' => $parameters
        ]);
    }

    private function checkParameters()
    {
        $matrixException = MatrixException::createWithSideAndRange($this->side, $this->range);
        $square = Square::createWithCoordinates($this->coordinateX, $this->coordinateY);

        $matrixException->setMiddleSquare($square);

        $matrixException->check();
    }

    private function createMatrix(): Matrix
    {
        $squares = Matrix::createSquares($this->side);

        return Matrix::createWithSideAndSquares($this->side, $squares);
    }


    private function prepareForTwig(Matrix $matrix, Matrix $subMatrix): array
    {
        $matrixTwig = $matrix->prepareForTwig();

        $subMatrixTwig = $subMatrix->prepareForTwig();
        $subMatrixTwig = array_merge(...$subMatrixTwig);

        return [$matrixTwig, $subMatrixTwig];
    }

    private function loadParameters(array $parameters): array
    {
        if (empty($parameters)) {
            $this->coordinateX = self::COORDINATE_X;
            $this->coordinateY = self::COORDINATE_Y;
            $this->side = self::SIDE;
            $this->range = self::RANGE;

            $parameters = [
                'coordinateX' => '',
                'coordinateY' => '',
                'side' => '',
                'range' => ''
            ];
        } else {
            $this->coordinateX = $parameters['coordinateX'];
            $this->coordinateY = $parameters['coordinateY'];
            $this->side = $parameters['side'];
            $this->range = $parameters['range'];
        }

        return $parameters;
    }
}

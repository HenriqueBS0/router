<?php

use HenriqueBS0\Router\Inner\PathPart;
use HenriqueBS0\Router\Inner\RouteModel;
use PHPUnit\Framework\TestCase;

class RouteModelTest extends TestCase
{

    public function testGetNumberPartsPath()
    {
        $routerModel = new RouteModel('user/{id}');

        $expectedNumberParts = 2;

        $this->assertEquals($expectedNumberParts, $routerModel->getNumberPartsPath());
    }

    public function testGetPathParts()
    {
        $routerModel = new RouteModel('user/{id}/note/{id}');

        $expectedPathParts = [new PathPart(0, 'user'), new PathPart(2, 'note')];

        $this->assertEquals($expectedPathParts, $routerModel->getPathParts());
    }

    public function testGetPositionParametersPath()
    {
        $routerModel = new RouteModel('user/{id}/note/{id}');

        $expectedPositionParametersPath = [
            ['id' => 1],
            ['id' => 3]
        ];

        $this->assertEquals($expectedPositionParametersPath, $routerModel->getPositionParametersPath());
    }

    public function testExplodeByBar()
    {
        $routerModel = new RouteModel('');

        $string = 'user/{id}';

        $expected = ['user', '{id}'];

        $this->assertEquals($expected, $routerModel->explodeByBar($string));
    }
}


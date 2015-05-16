<?php

namespace RapidRoute\Tests\Integration;

use RapidRoute\RapidRouteException;
use RapidRoute\RouteCollection;
use RapidRoute\MatchResult;

/**
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class EdgeCasesRouterTest extends RouterTestBase
{
    protected function compiledFileName()
    {
        return 'edge-cases';
    }

    protected function definitions(RouteCollection $routes)
    {
        $routes->get('/abc/{param}/bar', ['name' => 'middle-param']);
        $routes->get(['/123/{param}/bar', 'param' => '.*'], ['name' => 'all-middle-param']);
    }

    /**
     * Should return each case in the format:
     *
     * [
     *      'GET',
     *      '/user/1',
     *      RouterResult::found(['route_data'], ['id' => '1'])
     * ]
     *
     * @return array[]
     */
    public function routerMatchingExamples()
    {
        return [
            ['GET', '/abc//bar', MatchResult::notFound()],
            ['GET', '/abc/a/bar', MatchResult::found(['name' => 'middle-param'], ['param' => 'a'])],

            ['GET', '/123//bar', MatchResult::found(['name' => 'all-middle-param'], ['param' => ''])],
            ['GET', '/123/a/bar', MatchResult::found(['name' => 'all-middle-param'], ['param' => 'a'])],
        ];
    }

    public function testThrowsExceptionForNonEmptyRouteWithoutPrecedingSlash()
    {
        $this->setExpectedExceptionRegExp(RapidRouteException::getType(), '#prefixed with \'\\/\'#');
        $this->router->match('GET', 'abc/a/bar');
    }
}
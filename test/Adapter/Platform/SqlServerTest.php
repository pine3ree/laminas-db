<?php

/**
 * @see       https://github.com/laminas/laminas-db for the canonical source repository
 * @copyright https://github.com/laminas/laminas-db/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-db/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Db\Adapter\Platform;

use Laminas\Db\Adapter\Driver\Pdo\Pdo;
use Laminas\Db\Adapter\Platform\SqlServer;

class SqlServerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SqlServer
     */
    protected $platform;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->platform = new SqlServer;
    }

    /**
     * @covers Laminas\Db\Adapter\Platform\SqlServer::getName
     */
    public function testGetName()
    {
        $this->assertEquals('SQLServer', $this->platform->getName());
    }

    /**
     * @covers Laminas\Db\Adapter\Platform\SqlServer::getQuoteIdentifierSymbol
     */
    public function testGetQuoteIdentifierSymbol()
    {
        $this->assertEquals(array('[', ']'), $this->platform->getQuoteIdentifierSymbol());
    }

    /**
     * @covers Laminas\Db\Adapter\Platform\SqlServer::quoteIdentifier
     */
    public function testQuoteIdentifier()
    {
        $this->assertEquals('[identifier]', $this->platform->quoteIdentifier('identifier'));
    }

    /**
     * @covers Laminas\Db\Adapter\Platform\SqlServer::quoteIdentifierChain
     */
    public function testQuoteIdentifierChain()
    {
        $this->assertEquals('[identifier]', $this->platform->quoteIdentifierChain('identifier'));
        $this->assertEquals('[identifier]', $this->platform->quoteIdentifierChain(array('identifier')));
        $this->assertEquals('[schema].[identifier]', $this->platform->quoteIdentifierChain(array('schema','identifier')));
    }

    /**
     * @covers Laminas\Db\Adapter\Platform\SqlServer::getQuoteValueSymbol
     */
    public function testGetQuoteValueSymbol()
    {
        $this->assertEquals("'", $this->platform->getQuoteValueSymbol());
    }

    /**
     * @covers Laminas\Db\Adapter\Platform\SqlServer::quoteValue
     */
    public function testQuoteValue()
    {
        $this->setExpectedException(
            'PHPUnit_Framework_Error',
            'Attempting to quote a value in Laminas\Db\Adapter\Platform\SqlServer without extension/driver support can introduce security vulnerabilities in a production environment'
        );
        $this->assertEquals("'value'", $this->platform->quoteValue('value'));
    }

    /**
     * @covers Laminas\Db\Adapter\Platform\SqlServer::quoteTrustedValue
     */
    public function testQuoteTrustedValue()
    {
        $this->assertEquals("'value'", $this->platform->quoteTrustedValue('value'));
        $this->assertEquals("'Foo O''Bar'", $this->platform->quoteTrustedValue("Foo O'Bar"));
        $this->assertEquals("'''; DELETE FROM some_table; -- '", $this->platform->quoteTrustedValue('\'; DELETE FROM some_table; -- '));
        $this->assertEquals("'\\''; DELETE FROM some_table; -- '", $this->platform->quoteTrustedValue('\\\'; DELETE FROM some_table; -- '));
    }

    /**
     * @covers Laminas\Db\Adapter\Platform\SqlServer::quoteValueList
     */
    public function testQuoteValueList()
    {
        $this->setExpectedException(
            'PHPUnit_Framework_Error',
            'Attempting to quote a value in Laminas\Db\Adapter\Platform\SqlServer without extension/driver support can introduce security vulnerabilities in a production environment'
        );
        $this->assertEquals("'Foo O''Bar'", $this->platform->quoteValueList("Foo O'Bar"));
    }

    /**
     * @covers Laminas\Db\Adapter\Platform\SqlServer::getIdentifierSeparator
     */
    public function testGetIdentifierSeparator()
    {
        $this->assertEquals('.', $this->platform->getIdentifierSeparator());
    }

    /**
     * @covers Laminas\Db\Adapter\Platform\SqlServer::quoteIdentifierInFragment
     */
    public function testQuoteIdentifierInFragment()
    {
        $this->assertEquals('[foo].[bar]', $this->platform->quoteIdentifierInFragment('foo.bar'));
        $this->assertEquals('[foo] as [bar]', $this->platform->quoteIdentifierInFragment('foo as bar'));

        // single char words
        $this->assertEquals('([foo].[bar] = [boo].[baz])', $this->platform->quoteIdentifierInFragment('(foo.bar = boo.baz)', array('(', ')', '=')));

        // case insensitive safe words
        $this->assertEquals(
            '([foo].[bar] = [boo].[baz]) AND ([foo].[baz] = [boo].[baz])',
            $this->platform->quoteIdentifierInFragment('(foo.bar = boo.baz) AND (foo.baz = boo.baz)', array('(', ')', '=', 'and'))
        );
    }

    /**
     * @covers Laminas\Db\Adapter\Platform\SqlServer::setDriver
     */
    public function testSetDriver()
    {
        $driver = new Pdo(array('pdodriver' => 'sqlsrv'));
        $this->platform->setDriver($driver);
    }
}

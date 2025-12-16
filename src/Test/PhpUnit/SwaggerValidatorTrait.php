<?php

namespace WakeOnWeb\Component\Swagger\Test\PhpUnit;

use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Message\ResponseInterface;
use WakeOnWeb\Component\Swagger\Loader\JsonLoader;
use WakeOnWeb\Component\Swagger\Loader\YamlLoader;
use WakeOnWeb\Component\Swagger\SwaggerFactory;
use WakeOnWeb\Component\Swagger\Test\ContentValidator;
use WakeOnWeb\Component\Swagger\Test\JustinRainbowJsonSchemaValidator;
use WakeOnWeb\Component\Swagger\Test\SwaggerValidator;

/**
 * @author Quentin Schuler <q.schuler@wakeonweb.com>
 */
trait SwaggerValidatorTrait
{
    /**
     * @param string            $spec
     * @param ResponseInterface $response
     * @param string            $method
     * @param string            $path
     * @param int               $code
     */
    public static function assertResponseMatchSpec($spec, ResponseInterface $response, $method, $path, $code)
    {
        $factory = new SwaggerFactory(app(CacheItemPoolInterface::class));
        $factory->addLoader(new YamlLoader());
        $factory->addLoader(new JsonLoader());

        $swagger = $factory->buildFrom($spec);

        $contentValidator = new ContentValidator();
        $contentValidator->registerContentValidator(new JustinRainbowJsonSchemaValidator());

        $validator = new SwaggerValidator($swagger);
        $validator->registerResponseValidator($contentValidator);

        $validator->validateResponseFor($response, $method, $path, $code);
    }
}

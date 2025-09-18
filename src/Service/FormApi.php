<?php

namespace App\Service;

use App\Dto\Form;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\String\Slugger\AsciiSlugger;

class FormApi
{
    public string $slug = 'qcm';
    public Form $form;

    public function __construct()
    {
        $this->form = $this->loadForm();
    }

    /**
     * @return Form|array|null
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function loadForm(): Form|array|null
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        return $serializer->deserialize(
            file_get_contents(
                sprintf(
                    '%s/../../public/StaticApi/%s.json',
                    __DIR__,
                    $this->slug
                )
            ),
            Form::class,
            'json'
        );
    }

    public function getForm(): ?Form
    {
        return $this->form;
    }

    public function saveJsonResult(string $name, array $answers): void
    {
        try {

        file_put_contents(
            sprintf( '%s/../../results/%s_%s_%s.json'
                , __DIR__, $this->slug, $name, date('Y-m-d_His')
            ),
                json_encode($answers, JSON_PRETTY_PRINT|JSON_THROW_ON_ERROR|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE)
            );
        } catch (\Exception $e) {
            // todo : log message
            throw new HttpException(500, 'Fail to save results !');
        }
    }

    public function saveHtmlResult(string $name, string $html): void
    {

        $slugger = new AsciiSlugger();
        $name = $slugger->slug($name)->camel()->lower()->toString();

        file_put_contents(__DIR__ . '/../../results/'
            . $name . '_' . md5(date('YmdHis')) . '.html',
            $html
            );
    }
}

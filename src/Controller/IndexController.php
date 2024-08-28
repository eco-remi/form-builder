<?php

namespace App\Controller;

use App\Dto\Form;
use App\Service\FormApi;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    public function __construct(
        private FormApi $formApi,
        private string  $token = 'security',
    )
    {
    }

    #[Route('/form', name: 'form', methods: ['GET'])]
    public function displayForm(#[MapQueryParameter] ?string $slug, #[MapQueryParameter] ?string $name, Request $request): Response
    {
        if (empty($name) || empty($slug)) {
            throw new AccessDeniedHttpException();
        }

        // load static form
        $form = $this->formApi->getForm();

        // TODO if $this->formApi->getFormUuid() !== $slug
        // throw error

        return $this->render(
            'form.html.twig',
            [
                'slug' => $slug,
                'name' => $name,
                'form' => $form,
                'posted' => false,
                'answers' => [],
            ]
        );
    }

    #[Route('/form', name: 'postform', methods: ['POST'])]
    public function postForm(#[MapQueryParameter] ?string $slug, #[MapQueryParameter] ?string $name, Request $request): Response
    {
        if (empty($name) || empty($slug)) {
            throw new AccessDeniedHttpException();
        }

        // load static form
        $form = $this->formApi->getForm();

        // TODO if $this->formApi->getFormUuid() !== $slug
        // throw error

        $this->processResult($request, $form);

        return $this->redirectToRoute('formSuccess');

    }

    #[Route('/formSuccess', name: 'formSuccess', methods: ['get'])]
    public function formSuccess(#[MapQueryParameter] ?string $slug): Response
    {
        // load static form
        $form = $this->formApi->getForm();

        return $this->render(
            'index.html.twig',
            [
                'title' => $form->title,
                'message' => '<h2 style="text-align: center;padding-top: 20%;">Merci de votre participation ! 👍</h2>',
            ]
        );
    }

    #[Route('/form/submit/{fileName}', name: 'form_result_cache', methods: ['GET'])]
    public function displaySavedForm(string $fileName): Response
    {
        $path = __DIR__ . '/../../results/';

        if (!file_exists($path . $fileName)) {
            throw new NotFoundHttpException();
        }

        return $this->render(
            'index.html.twig',
            [
                'title' => 'Résultat ' . explode('_', $fileName)[0] ?? '',
                'message' => file_get_contents($path . $fileName),
            ]
        );
    }

    #[Route('/result', name: 'result', methods: ['GET'])]
    public function result(#[MapQueryParameter] string $token): Response
    {
        if ($token !== $this->token) {
            throw new AccessDeniedHttpException();
        }

        $results = [];
        // display json result
        foreach (scandir(__DIR__ . '/../../results/') as $result) {
            if (str_contains($result, '.json')) {
                $results[] = array_merge(explode('_', $result), [str_replace('.json', '.html', $result)]);
            }
        }
        // then cached result
        foreach (scandir(__DIR__ . '/../../results/') as $result) {
            if (str_contains($result, '.html')) {
                $results[] = [$result];
            }
        }

        return $this->render(
            'result.html.twig',
            [
                'title' => 'Résultats',
                'results' => $results,
            ]
        );
    }

    #[Route('/results/dl', name: 'resultsDownload', methods: ['GET'])]
    public function downloadResults(#[MapQueryParameter] string $token): Response
    {
        if ($token !== $this->token) {
            throw new AccessDeniedHttpException();
        }

        return $this->render(
            'index.html.twig',
            [
                'title' => 'TODO',
                'message' => '',
            ]
        );
    }

    public function processResult(Request $request, ?\App\Dto\Form $form): array
    {
        if ($request->getMethod() !== 'POST') {
            return [];
        }

        // update $form->items->result with class right|wrong|
        // ++ calculate result
        $answers = $this->checkResults($request->getPayload()->all(), $form);

        // save final rendering
        $this->formApi->saveHtmlResult($answers['name'],
            $this->render(
                'form.html.twig',
                [
                    'slug' => $answers['slug'] ?? 'error',
                    'name' => $answers['name'],
                    'form' => $form,
                    'posted' => true,
                    'answers' => $answers,
                ]
            )
        );

        // log full extra info for debug, monitoring and security check
        $answers['_SERVER'] = $_SERVER;

        // save result
        $this->formApi->saveJsonResult($answers['name'] . '_' . $answers['score'], $answers);


        return $answers;
    }

    public function checkResults(array $answers, ?Form $form): array
    {
        // display result source
        $answers['score'] = 0;
        foreach ($form->items as $i => $item) {
            $form->items[$i]['result'] = '';
            if (isset($answers[$item['uuid']])
                && $answers[$item['uuid']] == $item['answer']
            ) {
                $form->items[$i]['result'] = 'right';
                $answers['score']++;
            }
            if (isset($answers[$item['uuid']])
                && $answers[$item['uuid']] != $item['answer']
            ) {
                $form->items[$i]['result'] = 'wrong';
            }
        }
        if (empty($answers['name'])) {
            $answers['name'] = 'anonymous_' . rand(0, 1000);
        }

        return $answers;
    }
}
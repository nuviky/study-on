<?php

namespace App\Controller;

use App\DTO\CourseDTO;
use App\Entity\Course;
use App\Exception\BillingUnavailableException;
use App\Form\CourseType;
use App\Repository\CourseRepository;
use App\Service\BillingClient;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/courses')]
class CourseController extends AbstractController
{
    #[Route('/', name: 'course_index', methods: ['GET'])]
    public function index(CourseRepository $courseRepository, BillingClient $client): Response
    {
        $coursesR = $client->getCourses();

        if (!$this->getUser()){//Бесплатные
            $freeCourses = array_keys(array_column($coursesR, 'type'), 0);
            $freeCourses_ = [];
            foreach (array_keys(array_column($coursesR, 'type'), 0) as $course){
                $freeCourses_ [] = [
                    'course' => $courseRepository->findOneBy(['characterCode' => $coursesR[$course]['character_code']]),
                    'type' => $coursesR[$course]['type']
                ];
            }
            return $this->render('course/index.html.twig', [
                'courses' => $freeCourses_,
            ]);
        }

        $coursesF = $courseRepository->findAll();
        $transactions = $client->getTransactions(
            ['type' => 'payment', 'skip_expired' => true],
            $this->getUser()->getApiToken()
        );
        $res = [];
        foreach ($coursesF as $course){
            $idR = array_search($course->getCharacterCode(), array_column($coursesR, 'character_code'));
            $idT = array_search($course->getCharacterCode(), array_column($transactions, 'code'));
            if ($idT === false) {
                $idT = null;
            }
            if ($idR === false) {
                $idR = null;
            }
            $res [] = [
                'course' => $course,
                'type' => $coursesR[$idR]['type'],
                'price' => $coursesR[$idR]['price'] ?? null,
                'transaction' => $transactions[$idT] ?? null
            ];
        }
        return $this->render('course/index.html.twig', [
            'courses' => $res,
        ]);
    }

    #[Route('/new', name: 'course_new', methods: ['GET', 'POST'])]
    #[isGranted('ROLE_SUPER_ADMIN')]
    public function new(Request $request, CourseRepository $courseRepository, BillingClient $client, EntityManagerInterface $entityManager): Response
    {
        $course = new Course();
        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($courseRepository->findOneBy(['characterCode' => $course->getCharacterCode()])) {
                return $this->renderForm('course/new.html.twig', [
                    'course' => $course,
                    'form' => $form,
                ]);
            }
            $courseDto = new CourseDTO();
            $courseDto->character_code = $form->get('characterCode')->getData();
            $courseDto->type = $form->get('type')->getData();
            $courseDto->price = $form->get('price')->getData();
            try {
                $responce = $client->newCourse($this->getUser(), $courseDto);
                if ($responce['success'] === true) {
                    $entityManager->persist($course);
                    $entityManager->flush();
                    return $this->redirectToRoute(
                        'course_show',
                        ['id' => $course->getId()],
                        Response::HTTP_SEE_OTHER
                    );
                }
            } catch (BillingUnavailableException $exception) {
                return $this->renderForm('course/new.html.twig', [
                    'course' => $course,
                    'form' => $form,
                ]);
            }
        }

        return $this->renderForm('course/new.html.twig', [
            'course' => $course,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'course_show', methods: ['GET'])]
    public function show(Course $course, BillingClient $client): Response
    {
        $courseB = $client->getCourse($course->getCharacterCode());
        $courseT_ = $client->getTransactions(
            ['type' => 'payment', 'skip_expired' => true],
            $this->getUser()->getApiToken()
        );
        $test = array_keys(array_column($courseT_, 'code'), $course->getCharacterCode());
        if ($test){
            $courseT = $courseT_[$test[0]];
        }

        return $this->render('course/show.html.twig', [
            'course' => $course,
            'courseB' => $courseB,
            'courseT' => $courseT ?? null
        ]);
    }

    #[Route('/{id}/edit', name: 'course_edit', methods: ['GET', 'POST'])]
    #[isGranted('ROLE_SUPER_ADMIN')]
    public function edit(Request $request, Course $course, CourseRepository $courseRepository, BillingClient $client, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $courseDto = new CourseDTO();
            $courseDto->character_code = $form->get('characterCode')->getData();
            $courseDto->type = $form->get('type')->getData();
            $courseDto->price = $form->get('price')->getData();
            try {
                $responce = $client->newCourse($this->getUser(), $courseDto);
                if ($responce['success'] === true) {
                    $entityManager->flush();
                    return $this->redirectToRoute(
                        'course_show',
                        ['id' => $course->getId()],
                        Response::HTTP_SEE_OTHER
                    );
                }
            } catch (BillingUnavailableException $exception) {
                return $this->renderForm('course/new.html.twig', [
                    'course' => $course,
                    'form' => $form,
                ]);
            }
        }

        return $this->renderForm('course/edit.html.twig', [
            'course' => $course,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'course_delete', methods: ['POST'])]
    #[isGranted('ROLE_SUPER_ADMIN')]
    public function delete(Request $request, Course $course, CourseRepository $courseRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$course->getId(), $request->request->get('_token'))) {
            $courseRepository->remove($course);
        }

        return $this->redirectToRoute('course_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/pay', name: 'course_pay', methods: ['GET'])]
    public function pay(
        Course $course,
        BillingClient $client
    ): Response
    {
        $res = $client->pay($course, $this->getUser()->getApiToken());
        if (isset($res['success'])) {
            $this->addFlash('notice', 'Оплата прошла успешно');
        } else {
            $this->addFlash('notice', 'Недостаточно средств');
        }

        return $this->redirectToRoute('course_show', ['id' => $course->getId()]);
    }
}

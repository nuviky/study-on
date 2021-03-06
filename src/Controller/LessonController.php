<?php

namespace App\Controller;

use App\Entity\Lesson;
use App\Form\LessonType;
use App\Repository\CourseRepository;
use App\Repository\LessonRepository;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/lessons')]
class LessonController extends AbstractController
{
    #[Route('/', name: 'lesson_index', methods: ['GET'])]
    #[isGranted('ROLE_USER')]
    public function index(LessonRepository $lessonRepository): Response
    {
        return $this->render('lesson/index.html.twig', [
            'lessons' => $lessonRepository->findAll(),
        ]);
    }

    #[Route('/new/{id}', name: 'lesson_new', methods: ['GET', 'POST'])]
    #[isGranted('ROLE_SUPER_ADMIN')]
    public function new(
        $id,
        Request $request,
        LessonRepository $lessonRepository,
        CourseRepository $courseRepository
    ): Response {
        $lesson = new Lesson();
        $lesson->setCourse($courseRepository->find($id));
        $form = $this->createForm(LessonType::class, $lesson);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $lessonRepository->add($lesson);
            return $this->redirectToRoute('course_show', ['id' => $id], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('lesson/new.html.twig', [
            'lesson' => $lesson,
            'form' => $form,
            'courseId' => $id,
        ]);
    }

    #[Route('/{id}', name: 'lesson_show', methods: ['GET'])]
    public function show(Lesson $lesson): Response
    {
        return $this->render('lesson/show.html.twig', [
            'lesson' => $lesson,
        ]);
    }

    #[Route('/{id}/edit', name: 'lesson_edit', methods: ['GET', 'POST'])]
    #[isGranted('ROLE_SUPER_ADMIN')]
    public function edit(Request $request, Lesson $lesson, LessonRepository $lessonRepository): Response
    {
        $form = $this->createForm(LessonType::class, $lesson);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $lessonRepository->add($lesson);
            return $this->redirectToRoute('lesson_show', ['id' => $lesson->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('lesson/edit.html.twig', [
            'lesson' => $lesson,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'lesson_delete', methods: ['POST'])]
    #[isGranted('ROLE_SUPER_ADMIN')]
    public function delete(Request $request, Lesson $lesson, LessonRepository $lessonRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $lesson->getId(), $request->request->get('_token'))) {
            $lessonRepository->remove($lesson);
        }

        return $this->redirectToRoute('course_show', ['id' => $lesson->getCourse()->getId()], Response::HTTP_SEE_OTHER);
    }
}

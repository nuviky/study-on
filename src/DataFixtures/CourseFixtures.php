<?php

namespace App\DataFixtures;

use App\Entity\Lesson;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Course;

class CourseFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        //Курс С#
        $cSharpCourse = new Course();
        $cSharpCourse->setName('Факультет разработки на C#');
        $cSharpCourse->setDescription('Научитесь разрабатывать веб-сервисы и приложения,' .
            ' используя язык программирования C#. Получите практический опыт и реализуете' .
            ' 9 собственных проекта для портфолио.');
        $cSharpCourse->setCharacterCode('FDC#');

        $lesson = new Lesson();
        $cSharpCourse->addLesson($lesson);
        $lesson->setTitle('Основы программирования и введение в С#');
        $lesson->setLessonContent('Закрепите знания на практике и реализуете консольный' .
            ' файловый менеджер. IT-компании часто предлагают подобное тестовое задание по работе с файлами.');
        $lesson->setLessonNumber(1);

        $lesson = new Lesson();
        $cSharpCourse->addLesson($lesson);
        $lesson->setTitle('Погружение в C# и .NET');
        $lesson->setLessonContent('Используете полученные знания и создадите оконный файловый менеджер,' .
            ' а также простейший менеджер баз данных.');
        $lesson->setLessonNumber(2);

        $lesson = new Lesson();
        $cSharpCourse->addLesson($lesson);
        $lesson->setTitle('Веб-разработка с ASP.NET MVC Core');
        $lesson->setLessonContent('Разработаете микросервис для мониторинга загрузки сервера,' .
            'клиент для него, веб-интерфейс, агент по сбору информации и проброса в сервис мониторинга.');
        $lesson->setLessonNumber(3);

        $lesson = new Lesson();
        $cSharpCourse->addLesson($lesson);
        $lesson->setTitle('Современная Enterprise-разработка');
        $lesson->setLessonContent('Систематизируете знания и изучите всю' .
            'специфику коммерческой разработки.');
        $lesson->setLessonNumber(4);

        $manager->persist($cSharpCourse);

        //Курс С++
        $c2PlusCourse = new Course();
        $c2PlusCourse->setName('Факультет разработки на C++');
        $c2PlusCourse->setDescription('Станьте разработчиком на С++ с нуля. Вы изучите' .
            'язык программирования С++, научитесь создавать сетевые и мобильные приложения и ' .
            'реализовывать графические интерфейсы.');
        $c2PlusCourse->setCharacterCode('FDC++');

        $lesson = new Lesson();
        $c2PlusCourse->addLesson($lesson);
        $lesson->setTitle('Введение в C++. Фундаментальные знания');
        $lesson->setLessonContent('Начнёте осваивать технические основы профессии.' .
            'Узнаете базовые понятия Git и языков C/C++, получите навыки работы с операционной' .
            'системой Linux, алгоритмами и структурами данных императивного программирования.');
        $lesson->setLessonNumber(1);

        $lesson = new Lesson();
        $c2PlusCourse->addLesson($lesson);
        $lesson->setTitle('Применение C++.Понимание окружения');
        $lesson->setLessonContent('Познакомитесь с инструментарием разработчика,' .
            'не всегда напрямую связанным с программированием. Рассмотрите принципы ООП, ' .
            'сложные моменты программирования на C/C++, работу с сетями и базами данных.');
        $lesson->setLessonNumber(2);

        $lesson = new Lesson();
        $c2PlusCourse->addLesson($lesson);
        $lesson->setTitle('С++ в контексте. Оптимизация и тонкости');
        $lesson->setLessonContent('Научитесь использовать язык программирования C++ ' .
            'эффективно, узнаете об основных паттернах проектирования и создании графических' .
            'интерфейсов. Вы объедините все полученные знания о языке и начнёте использовать их ' .
            'в комплексе. Это позволит создавать более сложные и технологичные приложения.');
        $lesson->setLessonNumber(3);

        $lesson = new Lesson();
        $c2PlusCourse->addLesson($lesson);
        $lesson->setTitle('Современные технологии, где применяется C++');
        $lesson->setLessonContent('Погрузитесь в практическое программирование' .
            'и научитесь создавать современные приложения с использованием С++, которые ' .
            'можно добавить в портфолио. Приобретёте навык командной работы над проектом.');
        $lesson->setLessonNumber(4);

        $manager->persist($c2PlusCourse);


        //Курс Python
        $pythonCourse = new Course();
        $pythonCourse->setName('Факультет Python-разработки');
        $pythonCourse->setDescription('Получите одну из самых востребованных IT-профессий.' .
            'Вы освоите Python, научитесь писать программы и веб-приложения. Реализуете 7 проектов' .
            'для портфолио, а мы дадим гарантию трудоустройства.');
        $pythonCourse->setCharacterCode('FDP');

        $lesson = new Lesson();
        $pythonCourse->addLesson($lesson);
        $lesson->setTitle('Введение в backend-разработку');
        $lesson->setLessonContent('Вы получите навыки работы с базами данных и ОС Linux. ' .
            'Реализуете около 30 алгоритмов с ветвлениями, циклами и рекурсиями от простых до сложных.');
        $lesson->setLessonNumber(1);

        $lesson = new Lesson();
        $pythonCourse->addLesson($lesson);
        $lesson->setTitle('Frontend и backend веб-сервиса');
        $lesson->setLessonContent('Вы научитесь создавать быстрые и безопасные сайты. ' .
            'Создадите проект учебной платформы.' .
            'Изучите Django Framework: менеджеры моделей, отправка почтовых сообщений, создание ' .
            'и оптимизация сложных запросов к базе данных, работа с наборами форм, развёртывание' .
            'Django-проекта на web-сервере и тестирование.');
        $lesson->setLessonNumber(2);

        $lesson = new Lesson();
        $pythonCourse->addLesson($lesson);
        $lesson->setTitle('Продвинутый Python');
        $lesson->setLessonContent('Разработаете сетевой чат с возможностью создавать ' .
            'пользователей, искать и добавлять друзей, отправлять сообщения выбранному пользователю.' .
            'Изучите востребованный на рынке фреймворк Flask и создадите с его помощью новостной портал.' .
            'Познакомитесь с Django REST Framework в связке с ReactJS и создадите ещё один проект —' .
            'ToDo планировщик с разделением ролей, собственным API и документацией.' .
            'Прохождение курсов в данной четверти возможно в любом порядке.');
        $lesson->setLessonNumber(3);

        $lesson = new Lesson();
        $lesson->setCourseRelation($pythonCourse);
        $lesson->setTitle('Командная разработка выпускного проекта');
        $lesson->setLessonContent('Готовый бизнес-проект, разработанный в команде на основе'.
            'вашей идеи. Научитесь писать код на Python, организовывать взаимодействие сервера на ' .
            'базе Linux с сервером баз данных.');
        $lesson->setLessonNumber(4);

        $manager->persist($pythonCourse);

        $manager->flush();
    }
}

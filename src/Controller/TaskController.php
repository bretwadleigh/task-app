<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\Task;

class TaskController extends Controller
{

    
    /**
     * @Route("/", name="homepage")
     */

    public function indexAction() {

        $tasks = $this->getDoctrine()
            ->getRepository(Task::class)
            ->findBy([], ['dueDate' => 'DESC']);
            #->findAll();

        return $this->render('task/index.html.twig', [
            'controller_name' => 'TaskController',   
            'msg' => '',
            'tasks' => $tasks
        ]);
    }

    /**
     * @Route("/new", name="newtask")
     */
    public function new(Request $request)
    {

        #$entityManager = $this->getDoctrine()->getManager();

        $task = new Task();
        #$task->setTask('Write a task');
        #$task->setDueDate(new \DateTime('tomorrow'));
        #$task->setComplete(false);

        $form = $this->createFormBuilder($task)
            ->add('task', TextType::class)
            ->add('dueDate', DateType::class)
            ->add('complete', CheckboxType::class, array(
                'label' => 'Completed?',
                'required' => false
                ))
            ->add('save', SubmitType::class, array('label' => 'Create Task'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $task = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($task);
            $entityManager->flush();

            $this->addFlash('success', 'New Task Created!');
            return $this->redirectToRoute('homepage');

        }

        return $this->render('task/new.html.twig', [
            'controller_name' => 'TaskController',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/show/{id}", name="showtask")
     */
    public function show(Request $request, $id) {

        $task = $this->getDoctrine()
            ->getRepository(Task::class)
            ->find($id);

            if (!$task) {
                throw $this->createNotFoundException(
                    'No task found for id '.$id
                );
            }

            $form = $this->createFormBuilder($task)
            ->add('task', TextType::class, array(
                'attr' => array('size' => '65')
            ))
            ->add('dueDate', DateType::class)
            ->add('complete', CheckboxType::class, array(
                'label' => 'Completed?',
                'required' => false
                ))
            ->add('save', SubmitType::class, array('label' => 'Update Task'))
            ->getForm();

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
    
                $task = $form->getData();
    
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($task);
                $entityManager->flush();

                $this->addFlash('success', 'Task Updated!');
                return $this->redirectToRoute('homepage');
    
            }
    
            return $this->render('task/show.html.twig', [
                'controller_name' => 'TaskController',
                'form' => $form->createView(),
                'task' => $task
            ]);

    }
}

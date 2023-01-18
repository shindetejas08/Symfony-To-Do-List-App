<?php

namespace App\Controller;

use App\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

class ToDoListController extends AbstractController
{
    
   
    /**
     * @Route("/", name="app_to_do_list")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        $tasks = $doctrine->getRepository(Task::class)->findBy([],['id'=>'DESC']);//getRepository() responsible for peforming other operations on entities here(Task entity). findAll() gives all the tasks from DB. findBy()sorted the array in desc order to display task added at top
        return $this->render('index.html.twig',['tasks'=>$tasks]);
    }

    /**
     * @Route("/create", name="create_task", methods={"POST"})
     */
    public function create(ManagerRegistry $doctrine,Request $request)
    {
        $title = trim($request->request->get('title'));
        if(empty($title))
            return $this->redirectToRoute('app_to_do_list');
         
        //store data in DB using entity
        $entityManager = $doctrine->getManager();//getManager() saves data to DB
        $task = new Task;
        $task->setTitle($title);
        $entityManager->persist($task); //prepares data to be added to DB
        $entityManager->flush();//actually adds data in DB
        return $this->redirectToRoute('app_to_do_list');

    }

    /**
     * @Route("/switch-status/{id}", name="switch_status")
     */
    public function switchStatus(ManagerRegistry $doctrine,$id): Response
    {
        $entityManager = $doctrine->getManager();
        $task = $entityManager->getRepository(Task::class)->find($id);
        $task->setStatus( !$task->isStatus());
        $entityManager->flush();
        return $this->redirectToRoute('app_to_do_list');
    }
    /**
     * @Route("/delete-task/{id}", name="task_delete")
     */
    public function deleteTask(ManagerRegistry $doctrine, Task $id): Response //Task $id is param converter, ie symfony will automatically call the required the id from DB
    {
        $entityManager = $doctrine->getManager();
        $entityManager->remove($id);
        $entityManager->flush();
        return $this->redirectToRoute('app_to_do_list');
    }

}

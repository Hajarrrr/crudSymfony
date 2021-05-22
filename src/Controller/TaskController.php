<?php

namespace App\Controller;

use App\Entity\Message;
use App\Repository\TaskRepository;
use App\Service\TaskManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{

    /**
     * @Route("/", name="app_tasks")
     * @param TaskRepository $taskRepository
     * @return Response
     */
    public function Messages(TaskRepository $taskRepository): Response
    {
        $messages = $taskRepository->findAll();
        //$tasks = $taskRepository->findBy([], ['id' => 'DESC']);

        return $this->render('message/messages.html.twig', [
            'controller_name' => 'TaskController',
            'messages' => $messages,
        ]);
    }


    /**
     * @Route("/message/create", name="app_create_task")
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $message = new Message();
        $description = $request->request->get('description', null);

        if ($description !== null) {
            if (!empty($description)){
                $em = $this->getDoctrine()->getManager();
                $message->setDescription($description);
                $em->persist($message);
                $em->flush();
                $this->addFlash('success', 'message sent');

                return $this->redirectToRoute('app_tasks');
            }
            else{
                $this->addFlash('warning', 'El campo descripcion no puede estar vacio');
            }
        }
        return $this->render('message/create.html.twig', [
            'message' => $message
        ]);
    }


    /**
     * @Route("/message/edit/{id}", name="app_edit_task")
     * @param int $id
     * @param TaskRepository $taskRepository
     * @param Request $request
     * @return Response
     */
    public function edit(int $id, TaskRepository $taskRepository, Request $request): Response
    {
        // Find the task to update
        $message = $taskRepository->find($id);

        // Check if exists the task with this id
        if (!$message){
            throw $this->createNotFoundException();
        }

        // Check if the description ins not null && not empty
        $description = $request->request->get('description', null);
        if ($description !== null) {
            if (!empty($description)){
                $em = $this->getDoctrine()->getManager();
                $message->setDescription($description);
                $em->persist($message);
                $em->flush();
                $this->addFlash('success', 'Message edited!');

                return $this->redirectToRoute('app_tasks');
            }
            else{
                $this->addFlash('warning', 'El campo descripcion no puede estar vacio');
            }
        }

        return $this->render('message/edit.html.twig', [
            'message' => $message,
        ]);
    }


    /**
     * Con paramsConvert
     * @Route(
     *      "/message/editar-convert-param/{id}",
     *      name="app_edit_task_convert-param",
     * )
     * @param Message $message
     * @param Request $request
     * @return Response
     */
    public function editParamsConvert(Message $message, Request $request): Response
    {
        $descripcion = $request->request->get('description', null);
        if (null !== $descripcion) {
            if (!empty($descripcion)) {
                $em = $this->getDoctrine()->getManager();
                $message->setDescription($descripcion);
                $em->flush();
                $this->addFlash(
                    'success',
                    'Tarea editada correctamente!'
                );
                return $this->redirectToRoute('app_tasks');
            } else {
                $this->addFlash(
                    'warning',
                    'El campo "Descripción" es obligatorio'
                );
            }
        }
        return $this->render('message/edit.html.twig', [
            "message" => $message,
        ]);
    }


    /**
     * @Route("/message/eliminar/{id}", name="app_delete_task")
     * @param Task $task
     * @return Response
     */
    public function delete(Message $message): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($message);
        $em->flush();
        $this->addFlash(
            'success',
            'message deleted!'
        );

        return $this->redirectToRoute('app_tasks');
    }


    // ------------------- Methods with Task services ----------------------------------

    /**
     * @Route("/create/message-service", name="app_create_task_service")
     * @param TaskManager $taskManager
     * @param Request $request
     * @return Response
     */
    public function createService(TaskManager $taskManager, Request $request): Response
    {
        $description = $request->request->get('description', null);
        $message = new Message();
        if (null !== $description) {
            $message->setDescription($description);
            $errors = $taskManager->validateTask($message);

            if (empty($errors)) {
                $taskManager->createService($message);
                $this->addFlash(
                    'success',
                    'Message sent!'
                );
                return $this->redirectToRoute('app_tasks');
            } else {
                foreach ($errors as $error) {
                    $this->addFlash(
                        'warning',
                        $error->getMessage()
                    );
                }
            }
        }
        return $this->render('message/create.html.twig', [
            "message" => $message,
        ]);
    }


    /**
     * Con paramsConvert
     * @Route(
     *      "/edit/message-service/{id}",
     *      name="app_edit_task_service-params-convert",
     *      requirements={"id"="\d+"}
     * )
     * @param TaskManager $taskManager
     * @param Task $task
     * @param Request $request
     * @return Response
     */
    public function updateParamsConvertService(TaskManager $taskManager, Message $message, Request $request): Response
    {
        $description = $request->request->get('description', null);
        if (null !== $description) {
            $message->setDescription($description);
            $errors = $taskManager->validateTask($message);

            if (0 === count($errors)) {
                $taskManager->updateService($message);
                $this->addFlash(
                    'success',
                    'Tarea actualizada correctamente!'
                );
                return $this->redirectToRoute('app_tasks');
            } else {
                foreach ($errors as $error) {
                    $this->addFlash(
                        'warning',
                        $error->getMessage()
                    );
                }
            }
        }
        return $this->render('message/edit.html.twig', [
            "message" => $message,
        ]);
    }

    /**
     * Con paramsConvert
     * @Route(
     *      "/eliminar/message-service/{id}",
     *      name="app_delete_task_service"
     *
     * )
     * @param Message $message
     * @param TaskManager $taskManager
     * @return Response
     */
    public function deleteParamsConvertService(Message $message, TaskManager $taskManager): Response
    {
        $taskManager->deleteService($message);
        $this->addFlash(
            'success',
            'message deleted'
        );

        return $this->redirectToRoute('app_tasks');
    }

}

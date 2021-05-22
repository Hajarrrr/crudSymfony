<?php
namespace App\Service;

use App\Entity\Message;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TaskManager
{

    private EntityManagerInterface $em;
    private TaskRepository $taskRepository;
    private ValidatorInterface $validator;

    /**
     * TaskManager constructor.
     * @param EntityManagerInterface $em
     * @param TaskRepository $taskRepository
     * @param ValidatorInterface $validator
     */
    public function __construct(
        EntityManagerInterface $em,
        TaskRepository $taskRepository,
        ValidatorInterface $validator
    )
    {
        $this->em = $em;
        $this->taskRepository = $taskRepository;
        $this->validator = $validator;
    }


    /**
     * @param Message $message
     */
    public function createService(MessageMessage $message): void
    {
        $this->em->persist($message);
        $this->em->flush();
    }

    /**
     * @param Message $message
     */
    public function updateService(Message $message): void
    {
        $this->em->flush();
    }

    /**
     * @param Message $message
     */
    public function deleteService(Message $message): void
    {
        $this->em->remove($message);
        $this->em->flush();
    }


    /**
     * @param Message $message
     * @return ConstraintViolationList
     */
    public function validateTask(Message $message): ConstraintViolationList
    {

        // Con el bundle validator
        $errors = $this->validator->validate($message);
        return $errors;


        // Metodo clasico
        /*if (empty($tarea->getDescripcion()))
            $errores[] = "Campo 'descripción' obligatorio";

        $tareaCondescripcionIgual = $this->tareaRepository->buscarTareaPorDescripcion($tarea->getDescripcion());
        if (null !== $tareaCondescripcionIgual && $tarea->getId() !== $tareaCondescripcionIgual->getId()) {
            $errores[] = "Descripción repetida";
        }*/


    }

}
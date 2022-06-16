<?php

namespace Core\Domain\Notification;

use Core\Domain\Entity\BaseEntity;
use Illuminate\Support\Facades\Validator;

class VideoLaravelValidator implements ValidatorInterface
{
    public function validate(BaseEntity $baseEntity): void
    {
        $arrayOfEntityProperties = $this->convertEntityToArray($baseEntity);

        $validation = Validator::make($arrayOfEntityProperties, [
            'title' => ['required', 'min:3', 'max:255'],
            'description' => ['required', 'min:3', 'max:255'],
            'yearLaunched' => ['required', 'integer'],
            'duration' => ['required', 'integer'],
        ]);

        if ($validation->fails()) {
            $this->addErrors($validation->errors()->messages(), $baseEntity);
        }
    }

    private function convertEntityToArray(BaseEntity $baseEntity): array
    {
        return [
            'title' => $baseEntity->title,
            'description' => $baseEntity->description,
            'yearLaunched' => $baseEntity->yearLaunched,
            'duration' => $baseEntity->duration,
        ];
    }

    private function addErrors(array $errors, BaseEntity $baseEntity): void
    {
        foreach ($errors as $error) {
            $baseEntity->notification->addError([
                'context' => 'video',
                'message' => $error[0]
            ]);
        }
    }
}

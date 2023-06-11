<?php

namespace MyBlog\Core\Validator;

use MyBlog\Core\Validator\Rules\Rule;
use MyBlog\Dtos\RequestDtoInterface;
use ReflectionException;
use ReflectionProperty;

class Validator
{
    /**
     * @throws ReflectionException
     */
    public static function validate(RequestDtoInterface $dto): array
    {
        $errors = [];

        foreach ($dto->rules() as $property_name => $ruleSet)
        {
            $property = new ReflectionProperty($dto, $property_name);

            if($property->name === $property_name)
            {
                /** @var Rule $rule */
                foreach ($ruleSet as $rule)
                {
                    $result = $rule($property->getValue($dto), $property->name);

                    // if string - then error message returned
                    if(is_string($result)) $errors[] = $result;
                }
            }
        }

        return $errors;
    }


    public function validate2(array $input): bool|array
    {
        $foundErrors = [];
        $foundErrors['errors'] = [];
        $foundErrors['hasErrors'] = false;

        if(!$input['title'] && !$input['content']) {
            $foundErrors['errors'][] = 'В запросе должны присутствовать и заголовок и содержимое новости';
        }

        $title = $input['title'];
        $content = $input['content'];

        if(mb_strlen($title) < 10 && mb_strlen($content) < 10) {
            $foundErrors['errors'][] = 'Длина заголовка и/или содержимого не должна быть менее 10 символов';
        }

        $title = trim($title);
        $content = trim($content);

        if(count($foundErrors['errors']) > 0) {
            $foundErrors['hasErrors'] = true;
        }

        $data =  array_merge($foundErrors, ['title' => $title, 'content' => $content]);

        return $foundErrors['hasErrors'] ? $foundErrors : $data;
    }
}
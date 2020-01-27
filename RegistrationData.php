<?php

use Symfony\Component\Validator\Constraints as Assert;

class RegistrationData
{
    /**
     * @Assert\NotBlank(message="Le prénom est obligatoire !")
     * @Assert\Length(min=3, minMessage="Le prénom doit faire au moins 3 caractères")
     */
    public $firstName;

    /**
     * @Assert\NotBlank(message="Le nom de famille est obligatoire !")
     * @Assert\Length(min=3, minMessage="Le nom de famille doit faire au moins 3 caractères")
     */
    public $lastName;

    /**
     * @Assert\NotBlank(message="L'adresse email est obligatoire !")
     * @Assert\Email(message="L'adresse email être au format valide !")
     */
    public $email;

    /**
     * @Assert\NotBlank(message="Le téléphone est obligatoire !")
     */
    public $phone;

    /**
     * @Assert\NotBlank(message="Le prénom est obligatoire !")
     * @Assert\Regex(pattern="/^developer|tester$/", message="Le poste choisi n'est pas valide")
     */
    public $position;

    /**
     * On ne pose pas de validation ici car elle n'est pas nécessaire pour le formulaire d'édition
     * On pourrait néanmoins la mettre ici et ensuite utiliser les groupes de validation pour l'extraire
     * lors du edit.php
     */
    public $agreeTerms;
}

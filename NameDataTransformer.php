<?php

use Symfony\Component\Form\DataTransformerInterface;

/**
 * LES CLASSES DE TRANSFORMATION
 * --------------
 * Un DataTransformer peut se représenter sous la forme d'une classe simple qui implémente l'interface DataTransformerInterface
 * Elle possède donc deux méthodes obligatoires :
 * 1) transform($value) qui va transformer la donnée originale en ce que l'on souhaite afficher dans le form HTML
 * 2) reverseTransform($value) qui va transformer la donnée soumise par le form HTML en la donnée souhaitée au final
 */
class NameDataTransformer implements DataTransformerInterface
{
    public function transform($chaineMajuscules)
    {
        // On reçoit la chaine en MAJUSCULES (on imagine "LIOR" ou "CHAMLA")
        // et on veut la transformer en chaine classique ("Lior" ou "Chamla")
        return ucwords(strtolower($chaineMajuscules));
    }

    public function reverseTransform($chaineClassique)
    {
        // On reçoit la chaine classique (on imagine donc "Lior" ou "Chamla")
        // et on veut la transformer en chaine  majuscules
        return strtoupper($chaineClassique);
    }
}

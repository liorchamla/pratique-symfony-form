<?php

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * UN TYPE DE CHAMP PERSONNALISE :
 * -------------
 * Les types de champs sont représentés sous la forme de classes qui héritent de la classe AbstractType et se comportent
 * exactement comme un formulaire global (voir notre RegistrationType), les mêmes méthodes peuvent être définies !
 */
class NameType extends AbstractType
{
    /**
     * La méthode getParent() permet de faire comprendre au composant symfony/form de quel autre champ on est le plus
     * proche. Pour ma part, je veux que les noms et prénoms soient affichés sous forme de Text, donc j'indique que mon
     * parent le plus proche est le TextType::class. 
     * 
     * Mais je pourrais créer un champ qui soit sous forme de liste déroulante et donc indiquer ici plutôt un ChoiceType::class
     * 
     * Cela permet au composant symfony/form de cumuler les options possibles :
     * 1) Les options que je définis pour mon propre type (dans la fonction configureOptions())
     * 2) Les options déjà présentes et définies pour le type parent
     */
    public function getParent()
    {
        return TextType::class;
    }

    /**
     * Cette fonction marche exactement de la même façon que celle du RegistrationType !
     * La seule différence est que le $builder ici n'a pas pour vocation de créer un formulaire entier, mais de modifier uniquement
     * notre champ !
     * 
     * Nous allons nous en servir pour automatiquement attacher à notre champ le NameDataTransformer
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new NameDataTransformer());
    }
}

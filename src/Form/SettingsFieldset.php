<?php declare(strict_types=1);

namespace GuestPrivate\Form;

use Laminas\Form\Element;
use Laminas\Form\Fieldset;

class SettingsFieldset extends Fieldset
{
    /**
     * @var string
     */
    protected $label = 'Guest'; // @translate

    protected $elementGroups = [
        'guest' => 'Guest', // @translate
    ];

    public function init(): void
    {
        // Fields default when no site setting.

        $this
            ->setAttribute('id', 'guest')
            ->setOption('element_groups', $this->elementGroups)
            ->add([
                'name' => 'guestprivate_theme_login',
                'type' => Element\Checkbox::class,
                'options' => [
                    'element_group' => 'guest',
                    'label' => 'Apply the theme to the default login page', // @translate
                ],
                'attributes' => [
                    'id' => 'guestprivate_theme_login',
                    'required' => false,
                ],
            ])
        ;
    }
}

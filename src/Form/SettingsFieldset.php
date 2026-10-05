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
                'name' => 'guestprivate_redirect_top_to_login',
                'type' => Element\Checkbox::class,
                'options' => [
                    'element_group' => 'guest',
                    'label' => 'Redirect default top route to login when there is no public site', // @translate
                ],
                'attributes' => [
                    'id' => 'guestprivate_redirect_top_to_login',
                    'required' => false,
                ],
            ])
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
            ->add([
                'name' => 'guestprivate_restrict_api',
                'type' => Element\Radio::class,
                'options' => [
                    'element_group' => 'guest',
                    'label' => 'Restrict api to authenticated users', // @translate
                    'info' => 'This setting is useful when all sites are private, but resources are public. The api remains available with credentials and the local api remains available when logged.', // @translate
                    'value_options' => [
                        '' => 'No restriction', // @translate
                        'api' => 'Public api only', // @translate
                        'api_local' => 'Local api only', // @translate
                        'all' => 'Public api and local api', // @translate
                    ],
                ],
                'attributes' => [
                    'id' => 'guestprivate_restrict_api',
                    'value' => '',
                ],
            ])
        ;
    }
}

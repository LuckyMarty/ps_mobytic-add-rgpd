<?php

/**
 * 2007-2022 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2022 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class Mobytic_add_rgpd extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'mobytic_add_rgpd';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Mobytic';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Mobytic - Add - RGPD');
        $this->description = $this->l('Ajouter un POPUP RGPD');

        $this->confirmUninstall = $this->l('');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        $this->installTab();

        Configuration::updateValue('MOBYTIC_ADD_RGPD_LIVE_MODE', false);

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('backOfficeHeader') &&
            $this->registerHook('displayFooter');
    }

    public function installTab()
    {
        $response = true;

        // First check for parent tab
        $parentTabID = Tab::getIdFromClassName('AdminMobytic');

        if ($parentTabID) {
            $parentTab = new Tab($parentTabID);
        } else {
            $parentTab = new Tab();
            $parentTab->active = 1;
            $parentTab->name = array();
            $parentTab->class_name = "AdminMobytic";
            foreach (Language::getLanguages() as $lang) {
                $parentTab->name[$lang['id_lang']] = "Mobytic";
            }
            $parentTab->id_parent = 0;
            $parentTab->module = $this->name;
            $response &= $parentTab->add();
        }

        // Check for parent tab2
        $parentTab_2ID = Tab::getIdFromClassName('AdminMobyticAddons');
        if ($parentTab_2ID) {
            $parentTab_2 = new Tab($parentTab_2ID);
        } else {
            $parentTab_2 = new Tab();
            $parentTab_2->active = 1;
            $parentTab_2->name = array();
            $parentTab_2->class_name = "AdminMobyticAddons";
            foreach (Language::getLanguages() as $lang) {
                $parentTab_2->name[$lang['id_lang']] = "Addons";
            }
            $parentTab_2->id_parent = $parentTab->id;
            $parentTab_2->module = $this->name;
            $response &= $parentTab_2->add();
        }

        // Created tab
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = 'Admin' . $this->name;
        $tab->name = array();
        foreach (Language::getLanguages() as $lang) {
            $tab->name[$lang['id_lang']] = "RGPD";
        }
        $tab->id_parent = $parentTab_2->id;
        $tab->module = $this->name;
        $response &= $tab->add();

        return $response;
    }

    public function uninstall()
    {
        $this->uninstallTab();

        Configuration::deleteByName('MOBYTIC_ADD_RGPD_LIVE_MODE');

        return parent::uninstall();
    }

    public function uninstallTab()
    {
        $id_tab = Tab::getIdFromClassName('Admin' . $this->name);
        $tab = new Tab($id_tab);
        $tab->delete();
        return true;
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitMobytic_add_rgpdModule')) == true) {
            $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        $output = $this->context->smarty->fetch($this->local_path . 'views/templates/admin/configure.tpl');

        return $output . $this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitMobytic_add_rgpdModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Afficher'),
                        'name' => 'MOBYTIC_ADD_RGPD_LIVE_MODE',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Oui')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Non')
                            )
                        ),
                    ),
                    array(
                        'col' => 6,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-link"></i>',
                        'desc' => $this->l('Lien renvoyant vers la page de "Mentions légales"'),
                        'name' => 'MOBYTIC_ADD_RGPD_LINK',
                        'label' => $this->l('Lien'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array(
            'MOBYTIC_ADD_RGPD_LIVE_MODE' => Configuration::get('MOBYTIC_ADD_RGPD_LIVE_MODE', true),
            'MOBYTIC_ADD_RGPD_LINK' => Configuration::get('MOBYTIC_ADD_RGPD_LINK'),
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be loaded in the BO.
     */
    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name) {
            $this->context->controller->addJS($this->_path . 'views/js/back.js');
            $this->context->controller->addCSS($this->_path . 'views/css/back.css');
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path . 'views/js/front.js');
        $this->context->controller->addCSS($this->_path . 'views/css/front.css');
    }

    public function hookDisplayFooter()
    {
        $this->context->smarty->assign([
            'mobytic_rgpd_img' => $this->_path . 'views/img/cookie-bites.gif',
            'mobytic_rgpd_link' => Configuration::get('MOBYTIC_ADD_RGPD_LINK', Tools::getValue('MOBYTIC_ADD_RGPD_LINK')),
        ]);

        if (Configuration::get('MOBYTIC_ADD_RGPD_LIVE_MODE') == true) {
            return $this->display(__FILE__, 'mobytic_add_rgpd.tpl');
        }
    }
}

<div id="mobytic-rgpd-wrapper">

    <input type="checkbox" id="checkbox_mobytic_rgpd">
    <label for="checkbox_mobytic_rgpd" class="open">
        {l s='RGPD' d='Modules.Mobytic.RGPD.Boutons'}
    </label>

    <div id="mobytic-rgpd">

        <div class="mobytic-rgpd-body">
            <div class="mobytic-rgpd-body-header">
                <div class="title">
                    <h1 id="mobytic-rgpd-title">
                        <small>{l s='Salut c\'est nous ...' d='Modules.Mobytic.RGPD'}</small>
                        <span>{l s='Les Cookies !' d='Modules.Mobytic.RGPD'}</span>
                    </h1>
                </div>
                <div class="img" style="background-image: url('{$mobytic_rgpd_img}');"></div>
            </div>
            <p id="mobytic_rgpd_p">
                {l s='Nous nous soucions de vos données, et nous aimerions utiliser des cookies pour améliorer votre expérience.' d='Modules.Mobytic.RGPD'}
            </p>
        </div>
        
        <div class="mobytic-rgpd-footer" {if $mobytic_rgpd_link}style="grid-template-columns: repeat(3, 1fr) !important;"{/if}>
            {if $mobytic_rgpd_link}<a href="{$mobytic_rgpd_link}">{l s='Plus d\'infos' d='Modules.Mobytic.RGPD.Boutons'}</a>{/if}
            <a>
                <label for="checkbox_mobytic_rgpd">
                    {l s='Refuser' d='Modules.Mobytic.RGPD.Boutons'}
                </label>
            </a>
            <a>
                <label for="checkbox_mobytic_rgpd">
                    {l s='Accepter' d='Modules.Mobytic.RGPD.Boutons'}
                </label>
            </a>
        </div>

    </div>

</div>
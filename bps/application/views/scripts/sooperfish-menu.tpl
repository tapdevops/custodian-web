<!-- {{ sooperfish-menu.tpl -->
<ul class="sf-menu" id="nav">
<?php foreach ($this->userMenu as $menu => $menu2) : ?>
    <?php $target = (strtoupper($menu) == 'REPORT') ? 'target="_blank"' : ''; ?>
    <li>
        <a href="<?php echo is_array($menu2) ? 'javascript:void(0)' : $menu2; ?>">
            <?php echo ucwords(str_replace('-', ' ', $menu)); ?></a>
        <?php if (is_array($menu2)) : ?>
        <ul>
            <?php foreach ($menu2 as $submenu => $menu3) : ?>
            <?php $target = (strtoupper($submenu) == 'GENERATE') ? 'target="_blank"' : ''; ?>
            <li>
                <a href="<?php echo is_array($menu3) ? 'javascript:void(0)' : $menu3; ?>">
                    <?php echo ucwords(str_replace('-', ' ', $submenu)); ?></a>
                <?php if (is_array($menu3)) : ?>
                <ul>
                    <?php foreach ($menu3 as $subsubmenu => $menu4) : ?>
                    <li>
                        <a href="<?php echo is_array($menu4) ? 'javascript:void(0)' : $menu4; ?>" <?php echo $target; ?>>
                            <?php echo ucwords(str_replace('-', ' ', $subsubmenu)); ?></a>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>
    </li>
<?php endforeach; ?>
</ul>

<!--<ul class="sf-menu" id="nav">
    <li>
        <a href="javascript:void(0)">HOME</a>
    </li>
    <li>
        <a href="javascript:void(0)">PENGATURAN</a>
        <ul>
            <li>
                <a href="http://bpsho.local//ho-setup-master-user/main">USER</a>
            </li>
            <li>
                <a href="http://bpsho.local//ho-setup-hak-akses/main">HAK AKSES</a>
            </li>
        </ul>
    </li>
    <li>
        <a href="javascript:void(0)">PREBUDGETING</a>
        <ul>
            <li>
                <a href="http://bpsho.local//ho-prebudget-master/main">MASTER</a>
            </li>
            <li>
                <a href="http://bpsho.local//ho-prebudget-norma/main">NORMA</a>
            </li>
        </ul>
    </li>
    <li>
        <a href="javascript:void(0)">BUDGETING HO</a>
        <ul>
            <li>
                <a href="javascript:void(0)">OUTLOOK</a>
                <ul>
                    <li>
                        <a href="http://bpsho.local//ho-act-outlook/main">ACT & OUTLOOK</a>
                    </li>
                    <li>
                        <a href="http://bpsho.local//ho-summary-outlook/main">SUMMARY OUTLOOK</a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="http://bpsho.local//ho-rencana-kerja/main">RENCANA KERJA</a>
            </li>
            <li>
                <a href="http://bpsho.local//ho-capex/main">CAPEX HO</a>
            </li>
            <li>
                <a href="http://bpsho.local//ho-opex/main">OPEX HO</a>
            </li>
            <li>
                <a href="http://bpsho.local//ho-spd/main">SPD HO</a>
            </li>
        </ul>
    </li>
    <li>
        <a href="javascript:void(0)">REPORT</a>
        <ul>
            <li>
                <a href="http://bpsho.local//ho-report-summary/main">REPORT SUMMARY</a>
            </li>
            <li>
                <a href="http://bpsho.local//ho-valid-submit/main">VALIDATION & SUBMIT</a>
            </li>
        </ul>
    </li>
    <li>
        <a href="">LOGOUT</a>
    </li>
</ul>-->

<script type="text/javascript">
$(document).ready( function() {
    $('ul.sf-menu').sooperfish({
        dualColumn: 15,
        animationShow: {height: 'show'},
        speedShow: 300,
        easingHide: 'linear',
        animationHide: {opacity: 'hide', height: 'hide'},
        speedHide: 100,
        easingHide: 'linear'
    });
} );
</script>
<!-- sooperfish-menu.phtml }} -->


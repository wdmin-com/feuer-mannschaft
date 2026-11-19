(function (wp, config) {
    'use strict';

    if (!wp || !wp.blocks || !wp.element || !wp.components || !wp.blockEditor) {
        return;
    }

    var el = wp.element.createElement;
    var registerBlockType = wp.blocks.registerBlockType;
    var InspectorControls = wp.blockEditor.InspectorControls;
    var useBlockProps = wp.blockEditor.useBlockProps;
    var PanelBody = wp.components.PanelBody;
    var SelectControl = wp.components.SelectControl;
    var RangeControl = wp.components.RangeControl;
    var members = config && Array.isArray(config.members) ? config.members : [];

    function preview(title, description) {
        return el('div', useBlockProps({ className: 'feuer-mannschaft-block-placeholder' }),
            el('strong', null, title),
            el('p', null, description)
        );
    }

    function memberSelector(props, includeCurrent) {
        var options = members.slice();
        if (includeCurrent) {
            options[0] = { label: 'Aktuelles Profil', value: 0 };
        }
        return el(SelectControl, {
            label: 'Teilnehmer',
            value: props.attributes.memberId,
            options: options,
            onChange: function (value) { props.setAttributes({ memberId: parseInt(value, 10) || 0 }); }
        });
    }

    registerBlockType('feuer-mannschaft/member', {
        apiVersion: 3,
        title: 'Mannschaftsmitglied',
        icon: 'id',
        category: 'widgets',
        attributes: { memberId: { type: 'integer', default: 0 }, view: { type: 'string', default: 'card' } },
        edit: function (props) {
            return el(wp.element.Fragment, null,
                el(InspectorControls, null, el(PanelBody, { title: 'Einstellungen', initialOpen: true },
                    memberSelector(props, false),
                    el(SelectControl, { label: 'Darstellung', value: props.attributes.view, options: [
                        { label: 'Profilkarte', value: 'card' }, { label: 'Link', value: 'link' }
                    ], onChange: function (value) { props.setAttributes({ view: value }); } })
                )),
                preview('Mannschaftsmitglied', props.attributes.memberId ? 'Ausgewähltes Profil: ID ' + props.attributes.memberId : 'Wählen Sie ein Profil in den Block-Einstellungen.')
            );
        },
        save: function () { return null; }
    });

    registerBlockType('feuer-mannschaft/mentioned-members', {
        apiVersion: 3,
        title: 'Erwähnte Mannschaftsmitglieder',
        icon: 'groups',
        category: 'widgets',
        attributes: { view: { type: 'string', default: 'links' } },
        edit: function (props) {
            return el(wp.element.Fragment, null,
                el(InspectorControls, null, el(PanelBody, { title: 'Einstellungen', initialOpen: true },
                    el(SelectControl, { label: 'Darstellung', value: props.attributes.view, options: [
                        { label: 'Links', value: 'links' }, { label: 'Profilkarten', value: 'cards' }
                    ], onChange: function (value) { props.setAttributes({ view: value }); } })
                )),
                preview('Erwähnte Mannschaftsmitglieder', 'Im Frontend erscheinen die in diesem Beitrag ausgewählten Profile.')
            );
        },
        save: function () { return null; }
    });

    registerBlockType('feuer-mannschaft/member-posts', {
        apiVersion: 3,
        title: 'Beiträge eines Mitglieds',
        icon: 'admin-post',
        category: 'widgets',
        attributes: { memberId: { type: 'integer', default: 0 }, limit: { type: 'integer', default: 6 } },
        edit: function (props) {
            return el(wp.element.Fragment, null,
                el(InspectorControls, null, el(PanelBody, { title: 'Einstellungen', initialOpen: true },
                    memberSelector(props, true),
                    el(RangeControl, { label: 'Anzahl der Beiträge', value: props.attributes.limit, min: 1, max: 24,
                        onChange: function (value) { props.setAttributes({ limit: value || 6 }); } })
                )),
                preview('Beiträge eines Mitglieds', props.attributes.memberId ? 'Ausgewähltes Profil: ID ' + props.attributes.memberId : 'Auf einer Profilseite wird das Mitglied automatisch erkannt.')
            );
        },
        save: function () { return null; }
    });
}(window.wp, window.feuerMannschaftBlocks || {}));

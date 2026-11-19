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
            options[0] = { label: 'Текущий профиль', value: 0 };
        }
        return el(SelectControl, {
            label: 'Участник',
            value: props.attributes.memberId,
            options: options,
            onChange: function (value) { props.setAttributes({ memberId: parseInt(value, 10) || 0 }); }
        });
    }

    registerBlockType('feuer-mannschaft/member', {
        apiVersion: 3,
        title: 'Участник команды',
        icon: 'id',
        category: 'widgets',
        attributes: { memberId: { type: 'integer', default: 0 }, view: { type: 'string', default: 'card' } },
        edit: function (props) {
            return el(wp.element.Fragment, null,
                el(InspectorControls, null, el(PanelBody, { title: 'Настройки', initialOpen: true },
                    memberSelector(props, false),
                    el(SelectControl, { label: 'Вид', value: props.attributes.view, options: [
                        { label: 'Карточка', value: 'card' }, { label: 'Ссылка', value: 'link' }
                    ], onChange: function (value) { props.setAttributes({ view: value }); } })
                )),
                preview('Участник команды', props.attributes.memberId ? 'Выбран профиль ID ' + props.attributes.memberId : 'Выберите участника в настройках блока.')
            );
        },
        save: function () { return null; }
    });

    registerBlockType('feuer-mannschaft/mentioned-members', {
        apiVersion: 3,
        title: 'Участники публикации',
        icon: 'groups',
        category: 'widgets',
        attributes: { view: { type: 'string', default: 'links' } },
        edit: function (props) {
            return el(wp.element.Fragment, null,
                el(InspectorControls, null, el(PanelBody, { title: 'Настройки', initialOpen: true },
                    el(SelectControl, { label: 'Вид', value: props.attributes.view, options: [
                        { label: 'Ссылки', value: 'links' }, { label: 'Карточки', value: 'cards' }
                    ], onChange: function (value) { props.setAttributes({ view: value }); } })
                )),
                preview('Участники публикации', 'На сайте будут показаны профили, выбранные в поле упоминаний этой публикации.')
            );
        },
        save: function () { return null; }
    });

    registerBlockType('feuer-mannschaft/member-posts', {
        apiVersion: 3,
        title: 'Публикации участника',
        icon: 'admin-post',
        category: 'widgets',
        attributes: { memberId: { type: 'integer', default: 0 }, limit: { type: 'integer', default: 6 } },
        edit: function (props) {
            return el(wp.element.Fragment, null,
                el(InspectorControls, null, el(PanelBody, { title: 'Настройки', initialOpen: true },
                    memberSelector(props, true),
                    el(RangeControl, { label: 'Количество публикаций', value: props.attributes.limit, min: 1, max: 24,
                        onChange: function (value) { props.setAttributes({ limit: value || 6 }); } })
                )),
                preview('Публикации участника', props.attributes.memberId ? 'Профиль ID ' + props.attributes.memberId : 'На странице профиля участник определяется автоматически.')
            );
        },
        save: function () { return null; }
    });
}(window.wp, window.feuerMannschaftBlocks || {}));

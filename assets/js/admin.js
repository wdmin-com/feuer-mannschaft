function initFeuerMannschaftAdmin() {
    var adminData = window.feuerMannschaftAdminData || {};
    var profileWizard = document.querySelector('[data-profile-wizard]');

    if (profileWizard) {
        var wizardPanels = Array.prototype.slice.call(profileWizard.querySelectorAll('[data-wizard-panel]'));
        var wizardTargets = Array.prototype.slice.call(profileWizard.querySelectorAll('[data-wizard-target]'));
        var wizardBack = profileWizard.querySelector('[data-wizard-back]');
        var wizardNext = profileWizard.querySelector('[data-wizard-next]');
        var wizardSave = profileWizard.querySelector('[data-wizard-save]');
        var wizardProgress = profileWizard.querySelector('[data-wizard-progress]');
        var wizardProgressFill = profileWizard.querySelector('[data-wizard-progress-fill]');
        var wizardCounter = profileWizard.querySelector('[data-wizard-counter]');
        var wizardAnnouncement = profileWizard.querySelector('[data-wizard-announcement]');
        var wizardForm = profileWizard.closest('form');
        var wizardCurrentStep = 0;

        function validateWizardPanel(panel) {
            var requiredFields = panel.querySelectorAll('[data-wizard-required="true"]');
            var firstInvalidField = null;

            Array.prototype.forEach.call(requiredFields, function (field) {
                var isFilled = String(field.value || '').trim() !== '';
                field.setCustomValidity(isFilled ? '' : 'Bitte geben Sie diesen Namen ein.');
                if (!isFilled && !firstInvalidField) {
                    firstInvalidField = field;
                }
            });

            if (!firstInvalidField) {
                return true;
            }

            if (!panel.hidden) {
                firstInvalidField.focus();
                firstInvalidField.reportValidity();
            }
            return false;
        }

        function showWizardStep(stepIndex, shouldFocus) {
            wizardCurrentStep = Math.max(0, Math.min(stepIndex, wizardPanels.length - 1));
            profileWizard.classList.add('is-enhanced');

            wizardPanels.forEach(function (panel, index) {
                var isActive = index === wizardCurrentStep;
                panel.hidden = !isActive;
                panel.classList.toggle('is-active', isActive);
            });

            wizardTargets.forEach(function (button) {
                var targetIndex = parseInt(button.getAttribute('data-wizard-target'), 10);
                var isCurrent = targetIndex === wizardCurrentStep;
                button.disabled = targetIndex > wizardCurrentStep;
                if (isCurrent) {
                    button.setAttribute('aria-current', 'step');
                } else {
                    button.removeAttribute('aria-current');
                }
                button.classList.toggle('is-complete', targetIndex < wizardCurrentStep);
            });

            if (wizardBack) {
                wizardBack.disabled = wizardCurrentStep === 0;
            }
            if (wizardNext) {
                wizardNext.hidden = wizardCurrentStep === wizardPanels.length - 1;
            }
            if (wizardSave) {
                wizardSave.hidden = wizardCurrentStep !== wizardPanels.length - 1;
            }
            if (wizardProgress) {
                wizardProgress.setAttribute('aria-valuenow', String(wizardCurrentStep + 1));
            }
            if (wizardProgressFill) {
                wizardProgressFill.style.width = (((wizardCurrentStep + 1) / wizardPanels.length) * 100) + '%';
            }
            if (wizardCounter) {
                wizardCounter.textContent = (wizardCurrentStep + 1) + ' von ' + wizardPanels.length;
            }
            if (wizardAnnouncement) {
                var activeHeading = wizardPanels[wizardCurrentStep].querySelector('h3');
                wizardAnnouncement.textContent = 'Schritt ' + (wizardCurrentStep + 1) + ' von ' + wizardPanels.length + ': ' + (activeHeading ? activeHeading.textContent : 'Profilangaben');
            }

            if (shouldFocus) {
                var heading = wizardPanels[wizardCurrentStep].querySelector('h3');
                if (heading) {
                    heading.setAttribute('tabindex', '-1');
                    heading.focus();
                }
            }
        }

        wizardTargets.forEach(function (button) {
            button.addEventListener('click', function () {
                var targetIndex = parseInt(button.getAttribute('data-wizard-target'), 10);
                if (targetIndex <= wizardCurrentStep) {
                    showWizardStep(targetIndex, true);
                }
            });
        });

        if (wizardBack) {
            wizardBack.addEventListener('click', function () {
                showWizardStep(wizardCurrentStep - 1, true);
            });
        }

        if (wizardNext) {
            wizardNext.addEventListener('click', function () {
                if (validateWizardPanel(wizardPanels[wizardCurrentStep])) {
                    showWizardStep(wizardCurrentStep + 1, true);
                }
            });
        }

        if (wizardSave) {
            wizardSave.addEventListener('click', function () {
                if (!validateWizardPanel(wizardPanels[0])) {
                    showWizardStep(0, false);
                    var firstMissingName = wizardPanels[0].querySelector('[data-wizard-required="true"]:invalid');
                    if (firstMissingName) {
                        firstMissingName.focus();
                        firstMissingName.reportValidity();
                    }
                    return;
                }
                var nativePublishButton = document.getElementById('publish');
                if (nativePublishButton) {
                    var nativeLabel = nativePublishButton.value || nativePublishButton.textContent;
                    if (nativeLabel) {
                        wizardSave.textContent = nativeLabel.trim();
                    }
                    nativePublishButton.click();
                }
            });
        }

        if (wizardForm) {
            wizardForm.addEventListener('submit', function (event) {
                for (var index = 0; index < wizardPanels.length; index += 1) {
                    if (!validateWizardPanel(wizardPanels[index])) {
                        event.preventDefault();
                        showWizardStep(index, false);
                        var invalidField = wizardPanels[index].querySelector('[data-wizard-required="true"]:invalid');
                        if (invalidField) {
                            invalidField.focus();
                            invalidField.reportValidity();
                        }
                        return;
                    }
                }
            }, true);
        }

        showWizardStep(0, false);
    }

    var firstName = document.getElementById('feuer_mannschaft_first_name');
    var lastName = document.getElementById('feuer_mannschaft_last_name');
    var titleInput = document.getElementById('title');
    var isProfileList = document.body.classList.contains('feuer-mannschaft-profile-list');
    var addNewLink = document.querySelector('.page-title-action');
    var postsFilter = document.getElementById('posts-filter');

    if (isProfileList && addNewLink && adminData.addNewUrl) {
        addNewLink.href = adminData.addNewUrl;
    }

    if (isProfileList && postsFilter && !postsFilter.querySelector('input[name="feuer_mannschaft_profile"]')) {
        var hiddenProfile = document.createElement('input');
        hiddenProfile.type = 'hidden';
        hiddenProfile.name = 'feuer_mannschaft_profile';
        hiddenProfile.value = '1';
        postsFilter.appendChild(hiddenProfile);
    }

    if (firstName && lastName && titleInput) {
        var slugInput = document.getElementById('feuer_mannschaft_slug');
        var slugifyMemberName = function (value) {
            var normalized = String(value || '').trim().toLowerCase().replace(/[äöüß]/g, function (character) {
                return { ä: 'ae', ö: 'oe', ü: 'ue', ß: 'ss' }[character] || character;
            });
            if (typeof normalized.normalize === 'function') {
                normalized = normalized.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
            }
            return normalized.replace(/ß/g, 'ss').replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
        };
        var syncTitle = function () {
            var fullName = [firstName.value, lastName.value].join(' ').trim();

            if (!fullName) {
                return;
            }

            titleInput.value = fullName;
        };

        var initialNameSlug = slugifyMemberName([firstName.value, lastName.value].join(' '));
        var slugWasCustomized = !!(slugInput && slugInput.value && slugInput.value !== initialNameSlug);
        var syncProfileSlug = function () {
            if (slugInput && !slugWasCustomized) {
                slugInput.value = slugifyMemberName([firstName.value, lastName.value].join(' '));
            }
        };

        firstName.addEventListener('input', syncTitle);
        lastName.addEventListener('input', syncTitle);
        firstName.addEventListener('input', syncProfileSlug);
        lastName.addEventListener('input', syncProfileSlug);
        if (slugInput) {
            slugInput.addEventListener('input', function () {
                slugWasCustomized = true;
            });
        }
        syncTitle();
        syncProfileSlug();
    }

    var profilePreview = document.querySelector('[data-profile-preview]');

    if (profilePreview) {
        var previewName = profilePreview.querySelector('[data-preview-name]');
        var previewRank = profilePreview.querySelector('[data-preview-rank]');
        var previewFunction = profilePreview.querySelector('[data-preview-function]');
        var previewCategory = profilePreview.querySelector('[data-preview-category]');
        var previewPhoto = profilePreview.querySelector('[data-preview-photo]');
        var previewExcluded = profilePreview.querySelector('[data-preview-excluded]');
        var previewProgress = profilePreview.querySelector('[data-preview-progress]');
        var previewAvatar = profilePreview.querySelector('[data-profile-preview-avatar]');
        var categorySelect = document.getElementById('feuer_mannschaft_profile_category');
        var galleryUpload = document.getElementById('feuer_mannschaft_gallery_upload');
        var excludeInput = document.getElementById('feuer_mannschaft_exclude_from_count');

        var getCheckedValuesByName = function (needle) {
            return Array.prototype.map.call(document.querySelectorAll('input[type="checkbox"][name*="' + needle + '"]:checked'), function (input) {
                return input.value;
            }).filter(Boolean);
        };

        var setBadgeState = function (node, isOk) {
            if (!node) {
                return;
            }

            node.classList.toggle('is-ok', !!isOk);
            node.classList.toggle('is-warning', !isOk);
        };

        var updateProfilePreview = function () {
            var fullName = [firstName ? firstName.value : '', lastName ? lastName.value : ''].join(' ').trim();
            var displayName = fullName;
            if (profilePreview.getAttribute('data-name-format') === 'initial' && firstName && firstName.value.trim() && lastName && lastName.value.trim()) {
                displayName = firstName.value.trim().charAt(0) + '. ' + lastName.value.trim();
            }
            var rankValues = getCheckedValuesByName('[rank]');
            var functionValues = getCheckedValuesByName('[function]');
            var categoryLabel = categorySelect && categorySelect.selectedOptions.length
                ? categorySelect.selectedOptions[0].textContent.replace(/^-+\s*/, '').trim()
                : '';
            var hasUploadedPhoto = !!(galleryUpload && galleryUpload.files && galleryUpload.files.length);
            var hasPhoto = hasUploadedPhoto || (previewPhoto && previewPhoto.textContent !== 'Foto offen');
            var complete = 0;

            if (previewName) {
                previewName.textContent = displayName || 'Neues Mannschaft-Profil';
            }

            if (previewAvatar) {
                previewAvatar.classList.toggle('has-photo', hasPhoto);
                previewAvatar.classList.toggle('is-empty', !hasPhoto);
                previewAvatar.textContent = (fullName || 'M').charAt(0).toUpperCase();
            }

            if (previewRank) {
                previewRank.textContent = rankValues.length ? rankValues[0] : 'Dienstgrad offen';
            }

            if (previewFunction) {
                previewFunction.textContent = functionValues.length ? functionValues.join(', ') : 'Funktion offen';
            }

            if (previewCategory) {
                previewCategory.textContent = categoryLabel || 'Kategorie offen';
            }

            if (previewPhoto && hasUploadedPhoto) {
                previewPhoto.textContent = galleryUpload.files.length === 1 ? '1 neues Foto' : galleryUpload.files.length + ' neue Fotos';
                setBadgeState(previewPhoto, true);
            }

            if (previewExcluded && excludeInput) {
                previewExcluded.textContent = excludeInput.checked ? 'Nicht mitzählen' : 'Wird mitgezählt';
                previewExcluded.classList.toggle('is-warning', excludeInput.checked);
                previewExcluded.classList.toggle('is-ok', !excludeInput.checked);
            }

            if (fullName) {
                complete += 1;
            }

            if (categoryLabel) {
                complete += 1;
            }

            if (hasPhoto) {
                complete += 1;
            }

            if (previewProgress) {
                previewProgress.style.width = Math.round((complete / 3) * 100) + '%';
            }
        };

        [firstName, lastName, categorySelect, galleryUpload, excludeInput].forEach(function (node) {
            if (node) {
                node.addEventListener('input', updateProfilePreview);
                node.addEventListener('change', updateProfilePreview);
            }
        });

        document.addEventListener('change', function (event) {
            if (event.target.matches('input[type="checkbox"][name*="[rank]"], input[type="checkbox"][name*="[function]"]')) {
                updateProfilePreview();
            }
        });

        updateProfilePreview();
    }

    var createRepeaterRow = function (repeater) {
        var row = document.createElement('div');
        var input = document.createElement('input');
        var removeButton = document.createElement('button');

        row.className = 'feuer-mannschaft-repeater-row';
        row.setAttribute('data-repeater-row', '');

        input.type = 'text';
        input.className = 'widefat';
        input.name = repeater.getAttribute('data-repeater-name') || '';
        input.placeholder = repeater.getAttribute('data-repeater-placeholder') || '';

        removeButton.type = 'button';
        removeButton.className = 'button button-link-delete';
        removeButton.setAttribute('data-repeater-remove', '');
        removeButton.textContent = 'Entfernen';

        row.appendChild(input);
        row.appendChild(removeButton);

        return row;
    };

    document.querySelectorAll('[data-repeater]').forEach(function (repeater) {
        var rowsContainer = repeater.querySelector('[data-repeater-rows]');
        var addButton = repeater.querySelector('[data-repeater-add]');

        if (!rowsContainer || !addButton) {
            return;
        }

        var ensureEmptyRow = function () {
            if (!rowsContainer.querySelector('[data-repeater-row]')) {
                rowsContainer.appendChild(createRepeaterRow(repeater));
            }
        };

        addButton.addEventListener('click', function () {
            var row = createRepeaterRow(repeater);
            rowsContainer.appendChild(row);
            row.classList.add('is-added');

            var input = row.querySelector('input');
            if (input) {
                input.focus();
            }

            window.setTimeout(function () {
                row.classList.remove('is-added');
            }, 700);
        });

        repeater.addEventListener('click', function (event) {
            var removeButton = event.target.closest('[data-repeater-remove]');

            if (!removeButton) {
                return;
            }

            var row = removeButton.closest('[data-repeater-row]');

            if (row) {
                row.remove();
            }

            ensureEmptyRow();
        });

        ensureEmptyRow();
    });

    document.querySelectorAll('[data-qualification-expiries]').forEach(function (repeater) {
        var rows = repeater.querySelector('[data-expiry-rows]');
        var template = repeater.querySelector('[data-expiry-template]');
        var addButton = repeater.querySelector('[data-expiry-add]');
        var nextIndex = rows ? rows.querySelectorAll('[data-expiry-row]').length : 0;

        if (!rows || !template || !addButton) {
            return;
        }

        addButton.addEventListener('click', function () {
            rows.insertAdjacentHTML('beforeend', template.innerHTML.replace(/__INDEX__/g, String(nextIndex)));
            nextIndex += 1;
        });

        rows.addEventListener('click', function (event) {
            var removeButton = event.target.closest('[data-expiry-remove]');
            if (!removeButton) {
                return;
            }
            var row = removeButton.closest('[data-expiry-row]');
            if (row) {
                row.remove();
            }
        });
    });

    var createOptionRow = function (placeholder) {
        var row = document.createElement('div');
        var labelInput = document.createElement('input');
        var urlInput = document.createElement('input');
        var actions = document.createElement('div');
        var moveUpButton = document.createElement('button');
        var moveDownButton = document.createElement('button');
        var removeButton = document.createElement('button');

        row.className = 'feuer-mannschaft-repeater-row feuer-mannschaft-option-row';
        row.setAttribute('data-option-row', '');

        labelInput.type = 'text';
        labelInput.className = 'widefat';
        labelInput.setAttribute('data-option-field', 'label');
        labelInput.placeholder = placeholder || 'Eintrag';

        urlInput.type = 'url';
        urlInput.className = 'widefat';
        urlInput.setAttribute('data-option-field', 'url');
        urlInput.placeholder = 'https://example.de/';

        actions.className = 'feuer-mannschaft-option-actions';

        moveUpButton.type = 'button';
        moveUpButton.className = 'button button-secondary';
        moveUpButton.setAttribute('data-option-move-up', '');
        moveUpButton.textContent = 'Hoch';

        moveDownButton.type = 'button';
        moveDownButton.className = 'button button-secondary';
        moveDownButton.setAttribute('data-option-move-down', '');
        moveDownButton.textContent = 'Runter';

        removeButton.type = 'button';
        removeButton.className = 'button button-link-delete';
        removeButton.setAttribute('data-option-remove', '');
        removeButton.textContent = 'Entfernen';

        actions.appendChild(moveUpButton);
        actions.appendChild(moveDownButton);
        actions.appendChild(removeButton);

        row.appendChild(labelInput);
        row.appendChild(urlInput);
        row.appendChild(actions);

        return row;
    };

    var updateOptionCount = function (scope) {
        var panel = scope ? scope.closest('[data-field-group-panel]') : null;
        var countNode = panel ? panel.querySelector('[data-option-count]') : null;

        if (!countNode || !panel) {
            return;
        }

        var count = 0;

        panel.querySelectorAll('[data-option-row]').forEach(function (row) {
            var labelInput = row.querySelector('[data-option-field="label"]');
            if (labelInput && String(labelInput.value || '').trim()) {
                count += 1;
            }
        });

        countNode.textContent = String(count);
    };

    var syncOptionMoveButtons = function (repeater) {
        var rows = Array.prototype.slice.call(repeater.querySelectorAll('[data-option-row]'));

        rows.forEach(function (row, index) {
            var moveUpButton = row.querySelector('[data-option-move-up]');
            var moveDownButton = row.querySelector('[data-option-move-down]');

            if (moveUpButton) {
                moveUpButton.disabled = index === 0;
            }

            if (moveDownButton) {
                moveDownButton.disabled = index === rows.length - 1;
            }
        });
    };

    var renumberOptionRows = function (repeater) {
        var prefix = repeater.getAttribute('data-option-prefix') || '';
        var placeholder = repeater.getAttribute('data-option-placeholder') || 'Eintrag';

        repeater.querySelectorAll('[data-option-row]').forEach(function (row, index) {
            row.querySelectorAll('[data-option-field]').forEach(function (input) {
                var field = input.getAttribute('data-option-field');
                input.name = prefix ? prefix + '[' + index + '][' + field + ']' : '';

                if ('label' === field) {
                    input.placeholder = placeholder;
                }
            });
        });

        syncOptionMoveButtons(repeater);
        updateOptionCount(repeater);
    };

    var ensureOptionRow = function (repeater) {
        var rowsContainer = repeater.querySelector('[data-option-rows]');

        if (!rowsContainer) {
            return;
        }

        if (!rowsContainer.querySelector('[data-option-row]')) {
            rowsContainer.appendChild(createOptionRow(repeater.getAttribute('data-option-placeholder') || 'Eintrag'));
        }

        renumberOptionRows(repeater);
    };

    var updateCustomGroupTitle = function (group) {
        var titleNode = group.querySelector('[data-custom-group-title]');
        var labelInput = group.querySelector('[data-custom-group-field="label"]');
        var title = labelInput && String(labelInput.value || '').trim()
            ? String(labelInput.value || '').trim()
            : 'Neues Zusatzfeld';

        if (titleNode) {
            titleNode.textContent = title;
        }
    };

    var renumberCustomFieldGroups = function (container) {
        var baseName = container.getAttribute('data-custom-field-groups-name') || '';

        container.querySelectorAll('[data-custom-field-group]').forEach(function (group, index) {
            var groupPrefix = baseName + '[' + index + ']';
            var keyInput = group.querySelector('[data-custom-group-field="key"]');
            var labelInput = group.querySelector('[data-custom-group-field="label"]');
            var descriptionInput = group.querySelector('[data-custom-group-field="description"]');
            var multipleInput = group.querySelector('[data-custom-group-field="multiple"]');
            var optionRepeater = group.querySelector('[data-option-repeater]');

            if (keyInput) {
                keyInput.name = groupPrefix + '[key]';
            }

            if (labelInput) {
                labelInput.name = groupPrefix + '[label]';
            }

            if (descriptionInput) {
                descriptionInput.name = groupPrefix + '[description]';
            }

            if (multipleInput) {
                multipleInput.name = groupPrefix + '[multiple]';
            }

            if (optionRepeater) {
                optionRepeater.setAttribute('data-option-prefix', groupPrefix + '[options]');
                ensureOptionRow(optionRepeater);
            }

            updateCustomGroupTitle(group);
        });
    };

    var customFieldGroupContainer = document.querySelector('[data-custom-field-groups]');
    var customFieldGroupAddButton = document.querySelector('[data-custom-field-group-add]');

    document.querySelectorAll('[data-option-repeater]').forEach(function (repeater) {
        ensureOptionRow(repeater);
    });

    if (customFieldGroupContainer) {
        renumberCustomFieldGroups(customFieldGroupContainer);
    }

    if (customFieldGroupAddButton && customFieldGroupContainer) {
        customFieldGroupAddButton.addEventListener('click', function () {
            var template = document.getElementById('feuer-mannschaft-custom-field-group-template');

            if (!template || !('content' in template)) {
                return;
            }

            var fragment = template.content.cloneNode(true);
            var group = fragment.querySelector('[data-custom-field-group]');

            if (!group) {
                return;
            }

            customFieldGroupContainer.appendChild(fragment);
            renumberCustomFieldGroups(customFieldGroupContainer);

            var focusTarget = customFieldGroupContainer.querySelectorAll('[data-custom-field-group]')[customFieldGroupContainer.querySelectorAll('[data-custom-field-group]').length - 1];
            if (focusTarget) {
                var labelInput = focusTarget.querySelector('[data-custom-group-field="label"]');
                if (labelInput) {
                    labelInput.focus();
                }
            }
        });
    }

    document.addEventListener('click', function (event) {
        var optionAddButton = event.target.closest('[data-option-add]');
        if (optionAddButton) {
            var optionRepeater = optionAddButton.closest('[data-option-repeater]');
            var rowsContainer = optionRepeater ? optionRepeater.querySelector('[data-option-rows]') : null;

            if (optionRepeater && rowsContainer) {
                var row = createOptionRow(optionRepeater.getAttribute('data-option-placeholder') || 'Eintrag');
                rowsContainer.appendChild(row);
                row.classList.add('is-added');
                renumberOptionRows(optionRepeater);

                var labelInput = row.querySelector('[data-option-field="label"]');
                if (labelInput) {
                    labelInput.focus();
                }

                window.setTimeout(function () {
                    row.classList.remove('is-added');
                }, 700);
            }

            return;
        }

        var optionRemoveButton = event.target.closest('[data-option-remove]');
        if (optionRemoveButton) {
            var optionRow = optionRemoveButton.closest('[data-option-row]');
            var optionRepeaterForRemove = optionRemoveButton.closest('[data-option-repeater]');

            if (optionRow) {
                optionRow.remove();
            }

            if (optionRepeaterForRemove) {
                ensureOptionRow(optionRepeaterForRemove);
            }

            return;
        }

        var optionMoveUpButton = event.target.closest('[data-option-move-up]');
        if (optionMoveUpButton) {
            var optionRowForUp = optionMoveUpButton.closest('[data-option-row]');
            var optionRepeaterForUp = optionMoveUpButton.closest('[data-option-repeater]');
            var previousRow = optionRowForUp ? optionRowForUp.previousElementSibling : null;

            if (optionRowForUp && previousRow && optionRepeaterForUp) {
                optionRowForUp.parentNode.insertBefore(optionRowForUp, previousRow);
                renumberOptionRows(optionRepeaterForUp);
            }

            return;
        }

        var optionMoveDownButton = event.target.closest('[data-option-move-down]');
        if (optionMoveDownButton) {
            var optionRowForDown = optionMoveDownButton.closest('[data-option-row]');
            var optionRepeaterForDown = optionMoveDownButton.closest('[data-option-repeater]');
            var nextRow = optionRowForDown ? optionRowForDown.nextElementSibling : null;

            if (optionRowForDown && nextRow && optionRepeaterForDown) {
                optionRowForDown.parentNode.insertBefore(nextRow, optionRowForDown);
                renumberOptionRows(optionRepeaterForDown);
            }

            return;
        }

        var customGroupRemoveButton = event.target.closest('[data-custom-field-group-remove]');
        if (customGroupRemoveButton && customFieldGroupContainer) {
            var group = customGroupRemoveButton.closest('[data-custom-field-group]');
            if (group) {
                group.remove();
                renumberCustomFieldGroups(customFieldGroupContainer);
            }
        }
    });

    document.addEventListener('input', function (event) {
        var dirtyField = event.target.closest('.feuer-mannschaft-field');
        if (dirtyField) {
            dirtyField.classList.add('is-dirty');
        }

        if (event.target.matches('[data-option-field="label"]')) {
            var optionRepeater = event.target.closest('[data-option-repeater]');
            if (optionRepeater) {
                updateOptionCount(optionRepeater);
            }
        }

        if (event.target.matches('[data-custom-group-field="label"]')) {
            var customGroup = event.target.closest('[data-custom-field-group]');
            if (customGroup) {
                updateCustomGroupTitle(customGroup);
            }
        }
    });

    document.addEventListener('submit', function (event) {
        var submitter = event.submitter || (event.target ? event.target.querySelector('input[type="submit"], button[type="submit"]') : null);

        if (submitter && !submitter.classList.contains('feuer-mannschaft-admin-saving')) {
            submitter.classList.add('feuer-mannschaft-admin-saving');
        }
    });

    document.querySelectorAll('input[data-switch-exclusive]').forEach(function (input) {
        input.addEventListener('change', function () {
            if (!input.checked) {
                return;
            }

            var groupName = input.getAttribute('data-switch-exclusive');

            document.querySelectorAll('input[data-switch-exclusive="' + groupName + '"]').forEach(function (otherInput) {
                if (otherInput !== input) {
                    otherInput.checked = false;
                }
            });
        });
    });

    var defaultImagePicker = document.querySelector('[data-feuer-mannschaft-default-image-picker]');
    var defaultImageTexts = adminData.defaultProfileImage || null;

    if (defaultImagePicker && defaultImageTexts && window.wp && wp.media) {
        var defaultImageInput = defaultImagePicker.querySelector('[data-default-image-input]');
        var defaultImagePreview = defaultImagePicker.querySelector('[data-default-image-preview]');
        var defaultImageSelect = defaultImagePicker.querySelector('[data-default-image-select]');
        var defaultImageRemove = defaultImagePicker.querySelector('[data-default-image-remove]');
        var mediaFrame = null;

        var clearNode = function (node) {
            while (node && node.firstChild) {
                node.removeChild(node.firstChild);
            }
        };

        var renderEmptyPreview = function () {
            if (!defaultImagePreview) {
                return;
            }

            clearNode(defaultImagePreview);

            var text = document.createElement('p');
            text.className = 'description';
            text.textContent = defaultImageTexts.emptyNotice || 'Noch kein Standardfoto ausgewaehlt.';
            defaultImagePreview.appendChild(text);
        };

        var renderImagePreview = function (attachment) {
            if (!defaultImagePreview) {
                return;
            }

            clearNode(defaultImagePreview);

            var image = document.createElement('img');
            var sizes = attachment && attachment.sizes ? attachment.sizes : {};
            var source = '';

            if (sizes.medium_large && sizes.medium_large.url) {
                source = sizes.medium_large.url;
            } else if (sizes.medium && sizes.medium.url) {
                source = sizes.medium.url;
            } else if (sizes.thumbnail && sizes.thumbnail.url) {
                source = sizes.thumbnail.url;
            } else if (attachment && attachment.url) {
                source = attachment.url;
            }

            if (!source) {
                renderEmptyPreview();
                return;
            }

            image.src = source;
            image.alt = attachment.alt || attachment.title || '';
            image.className = 'feuer-mannschaft-default-image-preview-image';
            defaultImagePreview.appendChild(image);
        };

        var syncDefaultImageButtons = function () {
            var hasImage = defaultImageInput && defaultImageInput.value;

            if (defaultImageSelect) {
                defaultImageSelect.textContent = hasImage
                    ? (defaultImageTexts.replaceLabel || 'Standardfoto ersetzen')
                    : (defaultImageTexts.chooseLabel || 'Standardfoto auswaehlen');
            }

            if (defaultImageRemove) {
                defaultImageRemove.disabled = !hasImage;
            }
        };

        if (defaultImageSelect && defaultImageInput) {
            defaultImageSelect.addEventListener('click', function () {
                if (!mediaFrame) {
                    mediaFrame = wp.media({
                        title: defaultImageTexts.frameTitle || 'Standardfoto auswaehlen',
                        button: {
                            text: defaultImageTexts.frameButton || 'Dieses Foto verwenden'
                        },
                        library: {
                            type: 'image'
                        },
                        multiple: false
                    });

                    mediaFrame.on('select', function () {
                        var attachment = mediaFrame.state().get('selection').first().toJSON();

                        defaultImageInput.value = attachment.id || '';
                        renderImagePreview(attachment);
                        syncDefaultImageButtons();
                    });
                }

                mediaFrame.open();
            });
        }

        if (defaultImageRemove && defaultImageInput) {
            defaultImageRemove.addEventListener('click', function () {
                defaultImageInput.value = '';
                renderEmptyPreview();
                syncDefaultImageButtons();
            });
        }

        syncDefaultImageButtons();
    }

    var mentionsData = adminData.mentions || null;
    var mentionHelper = document.querySelector('[data-feuer-mannschaft-mention-helper]');

    if (!mentionsData || !mentionHelper) {
        return;
    }

    var mentionMembers = mentionsData.members || {};
    var mentionTexts = mentionsData.texts || {};
    var emptyNode = mentionHelper.querySelector('.feuer-mannschaft-mention-helper-empty');
    var listNode = mentionHelper.querySelector('.feuer-mannschaft-mention-shortcode-list');

    var getUniqueIds = function (values) {
        var unique = [];

        values.forEach(function (value) {
            var normalized = String(value || '').trim();

            if (!/^\d+$/.test(normalized) || unique.indexOf(normalized) !== -1) {
                return;
            }

            unique.push(normalized);
        });

        return unique;
    };

    var getSelectedMentionIds = function () {
        var fallbackSelect = document.querySelector('select[name="mentioned_persons[]"]');

        if (fallbackSelect) {
            return getUniqueIds(Array.prototype.map.call(fallbackSelect.selectedOptions || [], function (option) {
                return option.value;
            }));
        }

        var acfField = document.querySelector('.acf-field[data-name="mentioned_persons"]');

        if (!acfField) {
            return [];
        }

        var selectedItems = acfField.querySelectorAll('.values [data-id]');

        if (selectedItems.length) {
            return getUniqueIds(Array.prototype.map.call(selectedItems, function (item) {
                return item.getAttribute('data-id');
            }));
        }

        return getUniqueIds(Array.prototype.map.call(acfField.querySelectorAll('input[type="hidden"]'), function (input) {
            return input.value;
        }));
    };

    var selectInputText = function (input) {
        input.focus();
        input.select();
        input.setSelectionRange(0, input.value.length);
    };

    var copyText = function (text, input) {
        if (navigator.clipboard && navigator.clipboard.writeText) {
            return navigator.clipboard.writeText(text);
        }

        return new Promise(function (resolve, reject) {
            try {
                if (input) {
                    selectInputText(input);
                }

                if (document.execCommand('copy')) {
                    resolve();
                    return;
                }
            } catch (error) {
                reject(error);
                return;
            }

            reject(new Error('copy_failed'));
        });
    };

    var insertShortcodeIntoEditor = function (shortcode) {
        if (window.wp && wp.data && wp.blocks && wp.blocks.createBlock) {
            var blockDispatcher = wp.data.dispatch('core/block-editor');

            if (blockDispatcher && typeof blockDispatcher.insertBlocks === 'function') {
                blockDispatcher.insertBlocks(wp.blocks.createBlock('core/shortcode', {
                    text: shortcode
                }));

                return true;
            }
        }

        var classicEditor = document.getElementById('content');

        if (!classicEditor) {
            return false;
        }

        var start = typeof classicEditor.selectionStart === 'number' ? classicEditor.selectionStart : classicEditor.value.length;
        var end = typeof classicEditor.selectionEnd === 'number' ? classicEditor.selectionEnd : classicEditor.value.length;
        var before = classicEditor.value.slice(0, start);
        var after = classicEditor.value.slice(end);
        var spacerBefore = before && !/\s$/.test(before) ? ' ' : '';
        var spacerAfter = after && !/^\s/.test(after) ? ' ' : '';

        classicEditor.value = before + spacerBefore + shortcode + spacerAfter + after;
        classicEditor.focus();

        var caret = (before + spacerBefore + shortcode).length;

        if (typeof classicEditor.setSelectionRange === 'function') {
            classicEditor.setSelectionRange(caret, caret);
        }

        return true;
    };

    var flashButtonLabel = function (button, text) {
        var originalText = button.getAttribute('data-original-text') || button.textContent;

        button.setAttribute('data-original-text', originalText);
        button.textContent = text;

        window.setTimeout(function () {
            button.textContent = originalText;
        }, 1200);
    };

    var createMentionItem = function (member) {
        var item = document.createElement('article');
        var top = document.createElement('div');
        var strong = document.createElement('strong');
        var openLink = document.createElement('a');
        var preview = document.createElement('div');
        var previewLabel = document.createElement('span');
        var previewLink = document.createElement('a');
        var shortcodeLabel = document.createElement('label');
        var actions = document.createElement('div');
        var input = document.createElement('input');
        var copyButton = document.createElement('button');
        var insertButton = document.createElement('button');

        item.className = 'feuer-mannschaft-mention-shortcode-item';
        top.className = 'feuer-mannschaft-mention-shortcode-top';
        preview.className = 'feuer-mannschaft-mention-shortcode-preview';
        previewLabel.className = 'feuer-mannschaft-mention-shortcode-label';
        actions.className = 'feuer-mannschaft-mention-shortcode-actions';

        strong.textContent = member.name;

        openLink.href = member.url;
        openLink.target = '_blank';
        openLink.rel = 'noopener noreferrer';
        openLink.textContent = mentionTexts.openProfile || 'Profil öffnen';

        previewLabel.textContent = (mentionTexts.preview || 'Vorschau') + ':';

        previewLink.href = member.url;
        previewLink.target = '_blank';
        previewLink.rel = 'noopener noreferrer';
        previewLink.textContent = member.name;

        shortcodeLabel.className = 'feuer-mannschaft-mention-shortcode-label';
        shortcodeLabel.textContent = mentionTexts.shortcode || 'Kurzcode';
        shortcodeLabel.setAttribute('for', 'feuer-mannschaft-mention-shortcode-' + member.id);

        input.type = 'text';
        input.readOnly = true;
        input.className = 'widefat';
        input.id = 'feuer-mannschaft-mention-shortcode-' + member.id;
        input.value = member.shortcode;

        copyButton.type = 'button';
        copyButton.className = 'button button-secondary';
        copyButton.textContent = mentionTexts.copy || 'Kopieren';
        copyButton.addEventListener('click', function () {
            copyText(member.shortcode, input).then(function () {
                flashButtonLabel(copyButton, mentionTexts.copied || 'Kopiert');
            }).catch(function () {
                selectInputText(input);
            });
        });

        insertButton.type = 'button';
        insertButton.className = 'button button-secondary';
        insertButton.textContent = mentionTexts.insert || 'Einfügen';
        insertButton.addEventListener('click', function () {
            if (insertShortcodeIntoEditor(member.shortcode)) {
                flashButtonLabel(insertButton, mentionTexts.inserted || 'Eingefügt');
                return;
            }

            copyText(member.shortcode, input).then(function () {
                flashButtonLabel(insertButton, mentionTexts.copied || 'Kopiert');
            }).catch(function () {
                flashButtonLabel(insertButton, mentionTexts.insertFailed || 'Bitte den Kurzcode manuell kopieren.');
                selectInputText(input);
            });
        });

        top.appendChild(strong);
        top.appendChild(openLink);
        preview.appendChild(previewLabel);
        preview.appendChild(previewLink);
        actions.appendChild(input);
        actions.appendChild(copyButton);
        actions.appendChild(insertButton);

        item.appendChild(top);
        item.appendChild(preview);
        item.appendChild(shortcodeLabel);
        item.appendChild(actions);

        return item;
    };

    var renderMentionHelper = function () {
        var selectedIds = getSelectedMentionIds();

        if (!listNode || !emptyNode) {
            return;
        }

        listNode.innerHTML = '';

        if (!selectedIds.length) {
            emptyNode.hidden = false;
            listNode.hidden = true;
            emptyNode.textContent = mentionTexts.empty || 'Wählen Sie zuerst eine oder mehrere erwähnte Personen aus.';
            return;
        }

        selectedIds.forEach(function (id) {
            if (!mentionMembers[id]) {
                return;
            }

            listNode.appendChild(createMentionItem(mentionMembers[id]));
        });

        var hasItems = !!listNode.children.length;

        emptyNode.hidden = hasItems;
        listNode.hidden = !hasItems;

        if (!hasItems) {
            emptyNode.textContent = mentionTexts.empty || 'Wählen Sie zuerst eine oder mehrere erwähnte Personen aus.';
        }
    };

    var refreshMentionHelper = function () {
        window.requestAnimationFrame(renderMentionHelper);
    };

    var fallbackSelect = document.querySelector('select[name="mentioned_persons[]"]');

    if (fallbackSelect) {
        fallbackSelect.addEventListener('change', refreshMentionHelper);
    }

    var acfField = document.querySelector('.acf-field[data-name="mentioned_persons"]');

    if (acfField && typeof MutationObserver === 'function') {
        var observer = new MutationObserver(refreshMentionHelper);
        observer.observe(acfField, {
            childList: true,
            subtree: true,
            attributes: true
        });

        acfField.addEventListener('change', refreshMentionHelper);
        acfField.addEventListener('click', function () {
            window.setTimeout(refreshMentionHelper, 60);
        });
    }

    renderMentionHelper();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initFeuerMannschaftAdmin, { once: true });
} else {
    initFeuerMannschaftAdmin();
}
(function () {
    'use strict';
    var root = document.querySelector('[data-service-history]');
    if (!root) { return; }
    var rows = root.querySelector('[data-service-history-rows]');
    var template = root.querySelector('[data-service-history-template]');
    var add = root.querySelector('[data-service-history-add]');
    var nextIndex = rows.querySelectorAll('[data-service-history-row]').length;

    root.addEventListener('click', function (event) {
        var remove = event.target.closest('[data-service-history-remove]');
        if (remove) {
            event.preventDefault();
            remove.closest('[data-service-history-row]').remove();
        }
    });

    if (add && template) {
        add.addEventListener('click', function () {
            rows.insertAdjacentHTML('beforeend', template.innerHTML.replace(/__INDEX__/g, String(nextIndex++)));
            var created = rows.lastElementChild;
            if (created) { created.querySelector('input,select,textarea').focus(); }
        });
    }
}());

(function () {
    'use strict';
    var preview = document.querySelector('[data-profile-settings-preview]');
    if (!preview) { return; }
    var template = document.querySelector('[data-profile-template]');
    var height = document.getElementById('feuer_mannschaft_single_image_height');
    var photo = preview.querySelector('[data-profile-preview-photo]');
    var label = preview.querySelector('[data-profile-preview-label]');
    function update() {
        if (template) {
            preview.setAttribute('data-template', template.value);
            if (label) { label.textContent = template.options[template.selectedIndex].text; }
        }
        if (height && photo) {
            var heightValue = Math.max(240, Math.min(2000, parseInt(height.value, 10) || 620));
            photo.style.height = Math.min(360, Math.round(heightValue * 0.55)) + 'px';
        }
    }
    [template, height].forEach(function (field) {
        if (!field) { return; }
        field.addEventListener('input', update);
        field.addEventListener('change', update);
    });
    update();
}());

(function () {
    'use strict';
    var preview = document.querySelector('[data-card-settings-preview]');
    if (!preview) { return; }
    var image = preview.querySelector('.feuer-card-settings-preview-image');
    var content = preview.querySelector('.feuer-card-settings-preview-content');
    var fields = {
        overlay: document.getElementById('feuer_mannschaft_card_overlay_opacity'),
        darkeningEnabled: document.getElementById('feuer_mannschaft_card_overlay_enabled'),
        gradientEnabled: document.getElementById('feuer_mannschaft_card_gradient_enabled'),
        radius: document.getElementById('feuer_mannschaft_card_radius'),
        size: document.getElementById('feuer_mannschaft_card_name_size'),
        position: document.getElementById('feuer_mannschaft_card_text_position'),
        preset: document.getElementById('feuer_mannschaft_card_preset'),
        width: document.getElementById('feuer_mannschaft_card_image_width'),
        height: document.getElementById('feuer_mannschaft_card_image_height'),
        rank: document.querySelector('[name="feuer_mannschaft_settings[card_show_rank]"]'),
        role: document.querySelector('[name="feuer_mannschaft_settings[card_show_function]"]')
    };
    var overlayOutput = document.getElementById('feuer_mannschaft_card_overlay_value');
    var overlayRow = fields.overlay ? fields.overlay.closest('[data-control-row="overlay"]') : null;
    var positionRow = fields.position ? fields.position.closest('[data-control-row="position"]') : null;
    var ratioRows = [fields.width, fields.height].map(function (field) { return field ? field.closest('.feuer-settings-field') : null; });
    function update() {
        var photoOverlay = (parseInt(fields.overlay.value, 10) || 0) / 100;
        var width = parseInt(fields.width.value, 10) || 360;
        var height = parseInt(fields.height.value, 10) || 460;
        var preset = fields.preset.value;
        image.style.setProperty('--preview-overlay', String(photoOverlay));
        image.style.setProperty('--preview-radius', (parseInt(fields.radius.value, 10) || 0) + 'px');
        image.style.setProperty('--preview-name-size', (parseInt(fields.size.value, 10) || 24) + 'px');
        image.style.setProperty('--preview-card-ratio', width + ' / ' + height);
        content.style.justifyContent = fields.position.value === 'top' ? 'flex-start' : (fields.position.value === 'center' ? 'center' : 'flex-end');
        preview.setAttribute('data-preset',fields.preset.value);
        preview.setAttribute('data-darkening-enabled', fields.darkeningEnabled.checked ? '1' : '0');
        preview.setAttribute('data-gradient-enabled', fields.gradientEnabled.checked ? '1' : '0');
        preview.setAttribute('data-position',fields.position.value);
        preview.setAttribute('data-show-rank', fields.rank.checked ? '1' : '0');
        preview.setAttribute('data-show-function', fields.role.checked ? '1' : '0');
        if (overlayOutput) { overlayOutput.value = String(Math.round(photoOverlay * 100)); overlayOutput.textContent = overlayOutput.value; }
        var supportsOverlay = ['classic', 'minimal', 'dark', 'official'].indexOf(preset) !== -1;
        var overlayControlsEnabled = supportsOverlay && fields.darkeningEnabled.checked;
        if (overlayRow) {
            overlayRow.classList.toggle('feuer-mannschaft-control-inactive', !overlayControlsEnabled);
        }
        if (positionRow) {
            positionRow.classList.toggle('feuer-mannschaft-control-inactive', !supportsOverlay);
        }
        ratioRows.forEach(function (row) {
            if (!row) { return; }
            var inactive = preset === 'compact-horizontal';
            row.classList.toggle('feuer-mannschaft-control-inactive', inactive);
        });
    }
    Object.keys(fields).forEach(function (key) {
        if (!fields[key]) { return; }
        fields[key].addEventListener('input', update);
        fields[key].addEventListener('change', update);
    });
    update();
}());

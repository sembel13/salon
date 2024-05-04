<?php
/**
 * Файл подвала всех страниц (данный файл подключается на всех досутпных страницах для клиента)
 */
?>

        <script>
            $(document).ready(function() {
                $('.form-control:required, .form-select:required').each((index, el) => {
                    $(el).prev().append("<span style='color: red; font-size: 10px'> (Обязательно для заполнения)</span>")
                })
            });

            function removeElement(element, id, url) {
                if(!confirm("Вы действительно хотите удалить запись")) return null;

                $.ajax({
                    url: url + '?id=' + id,
                    type: 'delete',
                    success: (res) => {
                        $(element).parent().parent().remove();
                    },
                    error: (xhr, textStatus, error) => {
                        alert(xhr.responseText);
                    }
                })
            }

            window.dynamicField = {
                initField: function ($el, name, label, disabled = false, type = 'input', optionList = null) {
                    let indexField = 0;

                    const createDynamicField = (arrVal = null) => {
                        function getElement(index, value) {
                            let divElement = $('<div>', {
                                'class': 'mb-3'
                            });

                            let labelElement = $('<label>', {
                                'for': `${name}[${index}]`,
                                'class': 'form-label',
                                'text': label,
                                'css': {
                                    'display': 'block'
                                }
                            });

                            let inputElement = null;
                            if(type == 'input') {
                                let optionsinputElement = {
                                    'type': 'text',
                                    'class': 'form-control',
                                    'id': `${name}[${index}]`,
                                    'name': `${name}[${index}]`,
                                    'placeholder': label,
                                    'required': true,
                                    'value': value,
                                    'css': {
                                        'width': '92%',
                                        'display': 'inline-block'
                                    }
                                }

                                if(disabled) { optionsinputElement['disabled'] = true }

                                inputElement = $('<input>', optionsinputElement);
                            }

                            if(type == 'select') {
                                optionsinputElement = {
                                    'class': 'form-control',
                                    'id': `${name}[${index}]`,
                                    'name': `${name}[${index}]`,
                                    'aria-label': label,
                                    'required': true,
                                    'css': {
                                        'width': '92%',
                                        'display': 'inline-block'
                                    }
                                }

                                if(disabled) { optionsinputElement['disabled'] = true }

                                inputElement = $('<select>', optionsinputElement);

                                let option = {
                                    'value': '',
                                    'text': 'Выберите',
                                }

                                if(!value) { option['selected'] = true }
                                inputElement.append($('<option>', option))

                                for(let key in optionList) {
                                    let option = {
                                        'value': Number(key) + 1,
                                        'text': optionList[key],
                                    }

                                    if(value == (Number(key) + 1)) { option['selected'] = true }
                                    inputElement.append($('<option>', option))
                                }
                            }

                            let deleteButton = $('<button>', {
                                'class': 'btn btn-danger',
                                'text': 'Удалить',
                                'click': function() { $(this).parent().remove(); },
                                'css': {
                                    'display': 'inline-block',
                                    'margin-left': '10px',
                                    'margin-bottom': '4px'
                                }
                            });

                            divElement.append(labelElement);
                            divElement.append(inputElement);
                            divElement.append(deleteButton);

                            return divElement;
                        }

                        if(arrVal != null) {
                            arrVal.map((t) => {
                                const divElement = getElement(indexField++, t);
                                $el.children().last().before(divElement);
                            })

                            arrVal = null;
                        } else {
                            const divElement = getElement(indexField++);
                            $el.children().last().before(divElement);
                        }
                    }

                    const $btn = $el.find('.btn')
                    $btn.on('click', createDynamicField.bind(this, null))

                    return createDynamicField.bind(this)
                }
            }
        </script>

        <style>
            .color-red {
                color: red;
                cursor: pointer;
            }

            .btn {
                margin: 10px 0px;
            }

            .container {
                margin-top: 10px;
            }

            .inline-field {
                display: flex;
                justify-content: space-between;
                margin-bottom: 16px;
            }

            .inline-field > .half {
                width: 49%;
            }

            .multifield {
                margin-bottom: 10px;
            }

            .multifield .btn {
                margin-top: 0px;
            }
        </style>
    </body>
</html>
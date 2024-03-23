const server_address = '127.0.0.1:8081'; //INDIRIZZO E PORTA DEL SERVER (es. '127.0.0.1:8081')

document.addEventListener('DOMContentLoaded', () => populateTable()); //EVENT LISTENER per CARICAMENTO DELLA PAGINA

function populateTable() {
    const options = {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json'
        },
    };
    fetch('http://' + server_address + '/products', options)
        .then(response => {
            if (response.ok) {
                return response.json();
            }
            throw new Error('Errore nella richiesta: ' + response.statusText + response.status);
        })
        .then(responseData => {
            const dataArray = responseData.data;
            addRows(dataArray);
        })
        .catch(error => {
            console.error('Si è verificato un errore:', error);
        });
}

function openModal(action, row_id = 'row_-1') {
    const modal = new bootstrap.Modal(document.getElementById('modal'), {
        backdrop: 'static',
        keyboard: false
    });

    const id = parseInt(row_id.substring('row_'.length));

    setModal(action, id);

    modal.show();

    const submitButton = document.getElementById('button_submit');
    submitButton.addEventListener('click', () => {
        sendRequest(action, id, FormToJSON());
        modal.hide();
    });
}

function setModal(action, id) {
    const formInputs = document.querySelectorAll('#form_product input');

    switch (action) {
        case 'GET':
            document.getElementById('modalTitle').innerHTML = 'Show product';
            document.getElementById('button_submit').style.display = 'none';
            break;

        case 'POST':
            document.getElementById('modalTitle').innerHTML = 'Create product';
            document.getElementById('button_submit').innerHTML = 'Create';

            const idLabel = document.querySelector('label[for="id"]');
            idLabel.style.display = 'none';
            const idInput = document.getElementById('id');
            idInput.style.display = 'none';

            break;

        case 'PATCH':
            document.getElementById('modalTitle').innerHTML = 'Edit product';
            document.getElementById('button_submit').innerHTML = 'Edit';
            break;

        case 'DELETE':
            document.getElementById('modalTitle').innerHTML = 'Delete product';
            document.getElementById('button_submit').innerHTML = 'Delete';
            break;
    }
// MOSTRA IL PULSANTE SUBMIT SE L'AZIONE NON È 'GET'
    if (action !== 'GET') {
        document.getElementById('button_submit').style.display = 'block';
    }

// ABILITA O DISABILITA LA MODIFICA DEGLI INPUT IN BASE ALL'AZIONE
    formInputs.forEach(input => {
        if (action === 'POST' || action === 'PATCH') {
            if (input.id !== 'id') {
                input.removeAttribute('readonly');
                input.value = '';
            }
        } else {
            input.setAttribute('readonly', 'true');
        }
    });

// RIEMPIMENTO DEGLI INPUT TRAMITE QUERY SE L'AZIONE NON È 'POST'
    if (action !== 'POST') {
        getProduct(id)
            .then(data => {
                setFormAttributes(formInputs, data);
            })
            .catch(error => {
                console.error('Errore durante il recupero del prodotto:', error);
            });

        // MOSTRA L'ID NEL FORM
        const idLabel = document.querySelector('label[for="id"]');
        idLabel.style.display = 'block';
        const idInput = document.getElementById('id');
        idInput.style.display = 'block';
    } else {
        // NASCONDI L'ID DAL FORM SE L'AZIONE È 'POST'
        const idLabel = document.querySelector('label[for="id"]');
        idLabel.style.display = 'none';
        const idInput = document.getElementById('id');
        idInput.style.display = 'none';
        idInput.value = -1;
    }
}

function setFormAttributes(formInputs, data) {
    formInputs.forEach(input => {
        switch (input.id) {
            case 'id':
                input.value = data.id;
                break;
            case 'nome':
                input.value = data.attributes.nome;
                break;
            case 'marca':
                input.value = data.attributes.marca;
                break;
            case 'prezzo':
                input.value = data.attributes.prezzo;
                break;
        }
    });
}


function getProduct(id) {
    const options = {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json'
        },
    };
    return fetch('http://' + server_address + '/products/' + id, options)
        .then(response => {
            if (response.ok) {
                return response.json();
            }
            throw new Error('Errore nella richiesta: ' + response.statusText + response.status);
        })
        .then(responseData => {
            return responseData.data; //RITORNA L'ARRAY JSON POPOLATO CON GLI ATTRIBUTI
        })
        .catch(error => {
            console.error('Si è verificato un errore:', error);
        });
}

function sendRequest(action, id, data = []) {
    var url = '';
    const options = {
        method: action,
        headers: {
            //'Sec-Fetch-Mode': 'no-cors',
            'Content-Type': 'application/json',
        },
        body: action !== 'GET' && action !== 'DELETE' ? data : undefined
    };
    switch (action) {
        case 'POST':
            url = 'http://' + server_address + '/products';
            break;
        case 'GET':
        case 'PATCH':
        case 'DELETE':
            url = 'http://' + server_address + '/products/' + id;
            break;
    }

    fetch(url, options)
        .then(response => {
            if (response.ok) {
                return response.json();
            }
            throw new Error('Errore nella richiesta: ' + response.statusText);
        })
        .then(responseData => {
            if (action === 'POST') {
                addRows(responseData.data);
            } else if (action === 'PATCH') {

            }
            return responseData.data;
        })
        .catch(error => {
            console.error('Si è verificato un errore:', error);
        });

    if (action === 'DELETE') {
        document.getElementById('row_' + id).remove();
    }
}

function FormToJSON() {
    const form = document.getElementById('form_product');
    const formInputs = Array.from(form.querySelectorAll('input'));

    const formData = {
        data: {
            type: 'products',
            attributes: {}
        }
    };
    const idValue = parseInt(formInputs.find(input => input.id === 'id').value);
    if (idValue !== -1) {
        formData.data.id = idValue;
    }

    formInputs.forEach(input => {
        if (input.id !== 'id') {
            formData.data.attributes[input.id] = input.value;
        }
    });

    return JSON.stringify(formData);
}

function addRows(data) {
    const table = document.getElementById('product_table');
    data.forEach(item => {
        const row = table.insertRow();
        row.setAttribute('id', 'row_' + item.id);
        row.insertCell(0).textContent = item.id;
        row.insertCell(1).textContent = item.attributes.nome;
        row.insertCell(2).textContent = item.attributes.marca;
        row.insertCell(3).textContent = item.attributes.prezzo;

        //CELLA PER BOTTONI
        const cell = row.insertCell(4);

        const buttonGroup = document.createElement('div');
        buttonGroup.setAttribute('class', 'btn-group');
        buttonGroup.setAttribute('role', 'group');
        buttonGroup.setAttribute('aria-label', 'Basic example');

        const showButton = document.createElement('button');
        showButton.setAttribute('type', 'button');
        showButton.setAttribute('class', 'btn btn-secondary');
        showButton.textContent = 'Show';
        showButton.addEventListener('click', () => openModal('GET', row.id));

        const editButton = document.createElement('button');
        editButton.setAttribute('type', 'button');
        editButton.setAttribute('class', 'btn btn-secondary');
        editButton.textContent = 'Edit';
        editButton.addEventListener('click', () => openModal('PATCH', row.id));

        const deleteButton = document.createElement('button');
        deleteButton.setAttribute('type', 'button');
        deleteButton.setAttribute('class', 'btn btn-secondary');
        deleteButton.textContent = 'Delete';
        deleteButton.addEventListener('click', () => openModal('DELETE', row.id));

        buttonGroup.appendChild(showButton);
        buttonGroup.appendChild(editButton);
        buttonGroup.appendChild(deleteButton);

        cell.appendChild(buttonGroup);
    });
}

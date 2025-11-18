function vistarAviso(id, btnElemento) {
    const container = btnElemento.closest('.container-aviso');

    if (container) {
        container.classList.add("hidden");
    }

    fetch('marcar_aviso_visto', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ id: id })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Aviso ' + id + ' marcado como visto.');
            } else {
                console.error('Falha ao marcar aviso: ', data.error);
            }
        })
        .catch(error => {
            console.error('Erro na requisição fetch:', error);
        })
}
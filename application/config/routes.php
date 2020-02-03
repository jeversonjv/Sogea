<?php

defined('BASEPATH') OR exit('No direct script access allowed');


$route['default_controller'] = 'Inicio';
$route['404_override'] = 'MyCustom404Ctrl';
$route['translate_uri_dashes'] = FALSE;

//Welcome - Página inicial
$route['Inicio/evento/(:any)'] = 'Inicio/mostrar_evento/$1';
$route['Inicio/categoria/(:any)'] = 'Inicio/mostrar_evento_categoria/$1';
$route['Inicio/presenca/participante/buscar'] = 'Inicio/search_presenca_participante';
$route['Inicio/presenca/participante/gerar/(:any)/(:any)'] = 'Inicio/gerar_folha_presenca/$1/$2';

$route['Inicio/presenca/participante/folha'] = 'Inicio/search_folha_participante';
$route['Inicio/presenca/participante/gerar/folha/(:any)/(:any)'] = 'Inicio/gerar_folha_qrcode/$1/$2';


//Professores - Gerenciamento dos professores
$route['gerenciar/professores'] = 'client/Professores/gerenciar_professores';
$route['gerenciar/professores/horario/(:num)/(:num)'] = 'client/Professores/gerenciar_horario_professor/$1/$2';
$route['gerenciar/professores/editar/(:num)'] = 'client/Professores/editar_professor/$1';
$route['gerenciar/professores/adicionar'] = 'client/Professores/adicionar_professor';
$route['gerenciar/professores/excluir/(:num)'] = 'client/Professores/excluir_professor/$1';
$route['gerenciar/professores/editar/salvar'] = 'client/Professores/salvar_dados_professor';

$route['gerenciar/horario'] = 'client/Horario_professor/gerenciar_horario';
$route['gerenciar/horario/excluir/(:num)'] = 'client/Horario_professor/excluir_horario/$1';
$route['gerenciar/horario/adicionar'] = 'client/Horario_professor/adicionar_horario';


//Usuarios - Gerenciamento dos usuarios
$route['gerenciar/usuario/(:num)'] = 'client/Usuarios/gerenciar_usuarios/$1';
$route['gerenciar/usuarios/adicionar'] = 'client/Usuarios/adicionar_usuario';
$route['gerenciar/usuarios/editar/(:num)/(:num)'] = 'client/Usuarios/editar_usuario/$1/$2';
$route['gerenciar/usuarios/excluir/(:num)/(:num)'] = 'client/Usuarios/excluir_usuario/$1/$2';
$route['gerenciar/usuarios/perfil'] = 'client/Usuarios/gerenciar_perfil';
$route['gerenciar/usuarios/historico'] = 'client/Usuarios/historico_presenca';
$route['gerenciar/usuarios/historicos/(:num)'] = 'client/Usuarios/historico_presenca_usuario/$1';

//Eventos_tipos - Gerenciamento do tipo
$route['gerenciar/tipo'] = 'client/Eventos_tipos/gerenciar_tipo';
$route['gerenciar/tipo/editar/(:num)'] = 'client/Eventos_tipos/editar_tipo/$1';
$route['gerenciar/tipo/adicionar'] = 'client/Eventos_tipos/adicionar_tipo';
$route['gerenciar/tipo/excluir/(:num)'] = 'client/Eventos_tipos/excluir_tipo/$1';

//Eventos - Gerenciamento de eventos
$route['gerenciar/eventos/(:num)'] = 'client/Eventos/gerenciar_eventos/$1';
$route['gerenciar/eventos/alocar/(:num)/(:num)'] = 'client/Eventos/alocar_professor/$1/$2';
$route['gerenciar/eventos/editar/(:num)'] = 'client/Eventos/editar_evento/$1';
$route['gerenciar/eventos/visualizar/(:num)'] = 'client/Eventos/visualizar_evento/$1';
$route['gerenciar/eventos/adicionar'] = 'client/Eventos/adicionar_evento';
$route['gerenciar/eventos/excluir/(:num)'] = 'client/Eventos/excluir_evento/$1';
$route['gerenciar/eventos/toggle/ativo/(:num)/(:num)'] = 'client/Eventos/toggle_ativar_evento/$1/$2';
$route['gerenciar/eventos/toggle/estado/(:num)/(:num)'] = 'client/Eventos/toggle_estado/$1/$2';
$route['gerenciar/eventos/visualizar/toggle/adicionar/(:num)/(:num)'] = 'client/Eventos/toggle_alocar/$1/$2';
$route['gerenciar/eventos/visualizar/toggle/remover/(:num)/(:num)'] = 'client/Eventos/toggle_desalocar/$1/$2';
$route['gerenciar/eventos/editar/eventopai/(:num)'] = 'client/Eventos/editar_evento_pai/$1';
$route['gerenciar/eventos/encerrados'] = 'client/Eventos/visualizar_eventos_encerrados';
$route['gerenciar/eventos/ocorrer'] = 'client/Eventos/visualizar_eventos_ocorrer';
$route['gerenciar/eventos/validar'] = 'client/Eventos/validar_eventos';
$route['gerenciar/eventos/relatorio'] = 'client/Relatorio_evento/gerar_relatorio_eventos';
$route['gerenciar/eventos/relatorio/gerar/eventos/(:num)'] = 'client/Relatorio_evento/gerar_eventos/$1';
$route['gerenciar/eventos/relatorio/gerar/horario/(:num)'] = 'client/Relatorio_evento/gerar_horario/$1';


//Ocorrencia nos eventos.
$route['gerenciar/ocorrencias'] = 'client/Ocorrencia_eventos/gerenciar_ocorrencias';
$route['gerenciar/ocorrencias/evento/(:num)'] = 'client/Ocorrencia_eventos/ocorrencia_evento/$1';
$route['gerenciar/ocorrencias/adicionar/(:num)'] = 'client/Ocorrencia_eventos/adicionar_ocorrencia/$1';
$route['gerenciar/ocorrencias/editar/(:num)'] = 'client/Ocorrencia_eventos/editar_ocorrencia/$1';
$route['gerenciar/ocorrencias/excluir/(:num)/(:num)'] = 'client/Ocorrencia_eventos/delete_ocorrencia/$1/$2';

//Participantes - Gerenciamento de participantes
$route['gerenciar/participantes'] = 'client/Participantes';
$route['gerenciar/participantes/sistema'] = 'client/Participantes/gerenciar_participantes';
$route['gerenciar/participantes/sistema/adicionar'] = 'client/Participantes/adicionar_participante';
$route['gerenciar/participantes/sistema/editar/(:num)'] = 'client/Participantes/editar_participante/$1';
$route['gerenciar/participantes/sistema/excluir/(:num)'] = 'client/Participantes/excluir_participante/$1';

$route['gerenciar/participantes/eventos'] = 'client/Participantes_eventos/gerenciar_participantes_eventos';
$route['gerenciar/participantes/eventos/pai'] = 'client/Participantes_eventos/gerenciar_participantes_eventos_pai';
$route['gerenciar/participantes/eventos/manual/(:num)'] = 'client/Participantes_eventos/alocar_participantes_evento_manual/$1';
$route['gerenciar/participantes/eventos/manual/toggle/adicionar/(:num)/(:num)'] = 'client/Participantes_eventos/toggle_adicionar/$1/$2';
$route['gerenciar/participantes/eventos/manual/toggle/remover/(:num)/(:num)'] = 'client/Participantes_eventos/toggle_remover/$1/$2';
$route['gerenciar/participantes/eventos/manual/toggle/remover/(:num)/(:num)'] = 'client/Participantes_eventos/toggle_remover/$1/$2';
$route['gerenciar/participantes/eventos/desalocar/(:num)'] = 'client/Participantes_eventos/desalocar/$1';
$route['gerenciar/participantes/eventos/visualizar/(:num)'] = 'client/Participantes_eventos/visualizar_participantes/$1';
$route['gerenciar/participantes/eventos/planilha/(:num)'] = 'client/Participantes_eventos/alocar_participantes_planilha/$1';

//Presença - Gerenciamento de presenças
$route['gerenciar/presencas'] = 'client/Presenca/gerenciar_presenca_evento';
$route['gerenciar/presencas/gerar/(:num)'] = 'client/Presenca/gerar_folha/$1';

$route['gerenciar/presencas/pai'] = 'client/Presenca_pai/gerenciar_presenca_pai';
$route['gerenciar/presencas/pai/gerar/(:num)'] = 'client/Presenca_pai/gerar_folha/$1';

//Presença - Gerenciamento de relatorios 

$route['gerenciar/relatorio/geral'] = 'client/Relatorio/gerenciar_relatorio';
$route['gerenciar/relatorio/geral/gerar/(:num)'] = 'client/Relatorio/gerar_relatorio/$1';

$route['gerenciar/relatorio/pai'] = 'client/Relatorio_pai/gerenciar_relatorio_pai';
$route['gerenciar/relatorio/pai/gerar/(:num)'] = 'client/Relatorio_pai/gerar_relatorio/$1';

//Participantes - Gerenciamento de certificado

$route['gerenciar/certificado'] = 'client/Certificado/gerenciar_certificado';
$route['gerenciar/certificado/configurar/(:num)'] = 'client/Certificado/configurar_certificado/$1';
$route['gerenciar/certificado/participantes/(:num)/(:num)'] = 'client/Certificado/gerenciar_participantes_certificado/$1/$2';
$route['gerenciar/certificado/participantes/salvar/(:num)'] = 'client/Certificado/gerenciar_participantes_salvar/$1';
$route['gerenciar/certificado/pre_visualizar/(:num)'] = 'client/Certificado/pre_visualiazr/$1';
$route['gerenciar/certificado/gerar/(:num)'] = 'client/Certificado/gerar_certificado/$1';

//Participantes - Letiro de QrCode
$route['gerenciar/leitura/presenca'] = 'client/Presenca/abrir_camera';

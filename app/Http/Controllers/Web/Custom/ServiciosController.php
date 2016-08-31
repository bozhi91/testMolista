<?php

namespace App\Http\Controllers\Web\Custom;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\WebController;

class ServiciosController extends WebController
{

	public function getIndex()
	{
		return view("web.custom.servicios.index");
	}

	public function postForm()
	{
		$validator = \Validator::make($this->request->all(), [
			'nombre' => 'required',
			'apellidos' => 'required',
			'email' => 'required|email',
			'tipovivienda' => 'required',
			'telefono' => 'required',
			'direccion' => 'required',
		]);

		$tipo = $this->request->input('tipo');
		$nombre = $this->request->input('nombre');
		$apellidos = $this->request->input('apellidos');
		$email = $this->request->input('email');
		$tipovivienda = $this->request->input('tipovivienda');
		$telefono = $this->request->input('telefono');
		$direccion = $this->request->input('direccion');
		$comentario = $this->request->input('comentario');

		if ($validator->fails()) {
			return redirect()->back()->withErrors($validator)->withInput();
		}

		$content = "
			
			<!DOCTYPE html>
			<html>
			<head>
			<title> Solicitud de ". $tipo ." </title>
			</head>
			<body>

				<h1>Solicitud de ". $tipo ." </h1>
				<br>
				<h4> Nombre: ". $nombre ." </h4>
				<h4> Apellidos: ". $apellidos ." </h4>
				<h4> Email: ". $email ." </h4>
				<h4> Teléfono: ". $telefono ." </h4>
				<h4> Tipo de vivienda: ". $tipovivienda ." </h4>
				<h4> Dirección: ". $direccion ." </h4>
				<br>
				<p> ". $comentario ." </p>

			</body>
			</html>

		";

		$asunto = "Solicitud de ". $tipo;

		$sent = $this->site->sendEmail([
			'to' => "victor.saura@fincasbellamar.com",
			'subject' => $asunto,
			'content' => $content
		]);

		if ( !$sent )
		{
			return redirect()->back()->with('error', trans('general.messages.error'));
		}

		return redirect()->back()->with('success', trans('corporate/general.contact.success'));
	}

}

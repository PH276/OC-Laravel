<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Gestion\SondageGestion;
use App\Http\Requests\SondageRequest;

class SondageController extends Controller
{
	/**
	 * Instance de SondageGestion
	 *
	 * @var Lib\Gestion\SondageGestion
	 */
	protected $sondageGestion;

	/**
	 * Crée une nouvelle instance de SondageController
	 *
	 * @param Lib\Validation\SondageGestion $sondageGestion
	 * @return void
	 */
	public function __construct(SondageGestion $sondageGestion)
	{
		// On initialise la propriété pour la gestion
		$this->sondageGestion = $sondageGestion;
	}

	/**
	 * Traitement de l'URL de base : on affiche tous les sondages
	 *
	 * @return View
	 */
	public function index() 
	{
		return view('index')->withSondages($this->sondageGestion->getSondages());
	}

	/**
	 * Traitement de la demande du formulaire de vote
	 *
	 * @param  string $nom
	 * @return View
	 */
	public function create($nom)
	{
		// On récupère les données du sondage
		$sondage = $this->sondageGestion->getSondage($nom);
		// On crée le formulaire en transmettant les données et le nom du sondage
		return view('sondage', compact('sondage', 'nom'));
	}

	/**
	 * Traitement du formulaire de vote
	 *
	 * @param  App\Http\Requests\SondageRequest $request	 
	 * @param  string $nom
	 * @return Redirect
	 */
	public function store(SondageRequest $request, $nom)
	{
		// La validation a réussi 
		if($this->sondageGestion->save($nom, $request->all())) 
		{
			// On récupère les données du sondage
			$sondage = $this->sondageGestion->getSondage($nom);
			// On récupère les résultats
			$resultats = $this->sondageGestion->getResults($nom);
			// On envoie la page des résultats pour le votant
			return view('resultats', compact('sondage', 'nom', 'resultats'));
		}

		// L'Email a déjà été utilisé, on renvoie une erreur
		return back()->with('error', 'Désolé mais cet Email a déjà été utilisé pour ce sondage !')->withInput();
	}
}

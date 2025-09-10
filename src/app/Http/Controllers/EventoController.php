<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class EventoController extends Controller
{
    /**
     * Mostrar una lista de eventos (paginada).
     */
    public function index(): JsonResponse
    {
        // Evita traer TODO si la tabla crece
        $eventos = Evento::query()->latest()->paginate(50);

        return response()->json([
            'eventos' => $eventos->items(),
            'meta' => [
                'current_page' => $eventos->currentPage(),
                'last_page'    => $eventos->lastPage(),
                'per_page'     => $eventos->perPage(),
                'total'        => $eventos->total(),
            ],
            'status' => 200,
        ], 200);
    }

    /**
     * Crear un evento.
     */
    public function store(Request $request): JsonResponse
    {
        // Validación
        $validator = Validator::make($request->all(), [
            'titulo'       => 'required|string|max:255',
            'descripcion'  => 'required|string',
            'fecha_inicio' => 'required|date',
            'fecha_fin'    => 'required|date|after_or_equal:fecha_inicio',
            'ubicacion'    => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Datos faltantes o inválidos',
                'errors'  => $validator->errors(),
                'status'  => 400,
            ], 400);
        }

        $evento = Evento::create([
            'titulo'       => $request->input('titulo'),
            'descripcion'  => $request->input('descripcion'),
            'fecha_inicio' => $request->input('fecha_inicio'),
            'fecha_fin'    => $request->input('fecha_fin'),
            'ubicacion'    => $request->input('ubicacion'),
        ]);

        if (!$evento) {
            return response()->json([
                'message' => 'Error al crear el evento',
                'status'  => 500,
            ], 500);
        }

        return response()->json([
            'evento' => $evento,
            'status' => 201,
        ], 201);
    }

    /**
     * Mostrar un evento por ID.
     */
    public function show(int $id): JsonResponse
    {
        $evento = Evento::find($id);

        if (!$evento) {
            return response()->json([
                'message' => 'Evento no encontrado',
                'status'  => 404,
            ], 404);
        }

        return response()->json([
            'evento' => $evento,
            'status' => 200,
        ], 200);
    }

    /**
     * Actualizar un evento por ID.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $evento = Evento::find($id);

        if (!$evento) {
            return response()->json([
                'message' => 'Evento no encontrado',
                'status'  => 404,
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'titulo'       => 'required|string|max:255',
            'descripcion'  => 'required|string',
            'fecha_inicio' => 'required|date',
            'fecha_fin'    => 'required|date|after_or_equal:fecha_inicio',
            'ubicacion'    => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Datos faltantes o inválidos',
                'errors'  => $validator->errors(),
                'status'  => 400,
            ], 400);
        }

        $evento->update([
            'titulo'       => $request->input('titulo'),
            'descripcion'  => $request->input('descripcion'),
            'fecha_inicio' => $request->input('fecha_inicio'),
            'fecha_fin'    => $request->input('fecha_fin'),
            'ubicacion'    => $request->input('ubicacion'),
        ]);

        return response()->json([
            'evento' => $evento,
            'status' => 200,
        ], 200);
    }

    /**
     * Eliminar un evento por ID.
     */
    public function destroy(int $id): JsonResponse
    {
        $evento = Evento::find($id);

        if (!$evento) {
            return response()->json([
                'message' => 'Evento no encontrado',
                'status'  => 404,
            ], 404);
        }

        $evento->delete();

        return response()->json([
            'message' => 'Evento eliminado',
            'status'  => 200,
        ], 200);
    }
}

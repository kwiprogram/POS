<?php

namespace App\Http\Controllers;

use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TableController extends Controller
{
    public function index(Request $request)
    {
        $tables = Table::latest()->paginate(15);
        return view('tables.index', compact('tables'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'table_number' => ['required', 'string', 'max:50', 'unique:tables,table_number'],
            'capacity' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'in:available,occupied,reserved'],
        ]);

        Table::create($validated);

        return redirect()->route('tables.index')->with('success', 'Table added successfully.');
    }

    public function update(Request $request, Table $table)
    {
        $validated = $request->validate([
            'table_number' => ['required', 'string', 'max:50', Rule::unique('tables', 'table_number')->ignore($table->id)],
            'capacity' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'in:available,occupied,reserved'],
        ]);

        $table->update($validated);

        return redirect()->route('tables.index')->with('success', 'Table updated successfully.');
    }

    public function destroy(Table $table)
    {
        $table->delete();
        return redirect()->route('tables.index')->with('success', 'Table deleted successfully.');
    }
}

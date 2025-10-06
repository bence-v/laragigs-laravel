<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ListingController extends Controller
{
    public function index() {
        $listings = Listing::latest()->filter(request(['tag','search']))->paginate(3);
        return view('listings.index', ['listings' => $listings]);
    }

    public function show(Listing $listing) {
        return view('listings.show', ['listing' => $listing]);
    }

    public function create() {
        return view('listings.create');
    }

    public function store(Request $request) {
        $formField = $request->validate([
            'title' => 'required',
            'company' => ['required', Rule::unique('listings', 'company')],
            'location' => 'required',
            'website' => 'required',
            'email' => 'required|email',
            'tags' => 'required',
            'description' => 'required',
        ]);

        if($request->hasfile('logo')) {
            $formField['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $formField['user_id'] = auth()->id();

        Listing::create($formField);
        return redirect('/')->with('message', 'Listing created successfully!');
    }

    public function edit(Listing $listing) {
        return view('listings.edit', ['listing' => $listing]);
    }

    public function update(Request $request, Listing $listing) {
        if(auth()->user()->id != $listing->user_id) {
            return redirect('/')->with('error', 'Unauthorized Action');
        }
        $formField = $request->validate([
            'title' => 'required',
            'company' => ['required'],
            'location' => 'required',
            'website' => 'required',
            'email' => 'required|email',
            'tags' => 'required',
            'description' => 'required',
        ]);

        if($request->hasfile('logo')) {
            $formField['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $listing->update($formField);

        return back()->with('message', 'Listing updated successfully!');
    }

    public function destroy(Listing $listing) {
        if(auth()->user()->id != $listing->user_id) {
            return redirect('/')->with('error', 'Unauthorized Action');
        }
        $listing->delete();
        return redirect('/')->with('message', 'Listing deleted successfully!');
    }

    public function manage() {
        return view('listings.manage', ['listings' => auth()->user()->listings()->get()]);
    }
}

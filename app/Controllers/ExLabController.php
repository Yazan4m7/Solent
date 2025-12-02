<?php
/**
 * External Labs Controller
 *
 * This controller handles all operations related to external dental labs
 * that the system integrates with. External labs are third-party facilities
 * that may provide specialized services or materials to the main lab.
 *
 * @package App\Http\Controllers
 * @author Yazan
 * @created 10/4/2021
 * @version 1.0
 */
namespace App\Http\Controllers;

use App\Http\Middleware\RejectCaseMiddleware;
use Illuminate\Http\Request;
use App\lab;

/**
 * ExLabController manages external labs in the dental management system
 *
 * This controller provides functionality for listing, creating, and updating
 * external lab records. External labs are partnered facilities that may provide
 * specialized services or collaborate on certain dental cases.
 */
class ExLabController extends Controller
{
    /**
     * Display a listing of external labs with optional filtering
     *
     * This method retrieves all labs or a filtered subset based on request parameters.
     * It returns the labs index view with the requested lab data.
     *
     * @param Request $request The HTTP request containing optional filter parameters
     * @return \Illuminate\View\View The labs index view with lab data
     */
    public function index(Request $request){
        // Get selected lab IDs from request
        $selectedLabsIds = $request->labs;
        
        // Get all labs for dropdown options
        $labs = lab::all();
        
        // Filter labs if specific labs were selected and "all" wasn't included
        if(isset($request->labs) && !in_array("all", $request->labs)) {
            $selectedLabs = lab::whereIn('id', $request->labs)->get();
        } else {
            $selectedLabs = lab::all();
        }

        // Return the view with labs data and date filters
        return view('labs.index', compact("labs", "selectedLabs", "selectedLabsIds"))
            ->with('from', $request->from)
            ->with('to', $request->to);
    }

    /**
     * Show the form for creating a new external lab
     *
     * @return \Illuminate\View\View The lab creation form view
     */
    public function returnCreate()
    {
        return view('labs.create');
    }
    
    /**
     * Store a newly created external lab in the database
     *
     * This method validates the submitted lab data and creates a new
     * lab record in the database.
     *
     * @param Request $request The HTTP request containing lab form data
     * @return \Illuminate\Http\RedirectResponse Redirect back with success message
     */
    public function create(Request $request)
    {
        // Validate the lab data
        $this->validate($request, [
            'lab_name' => 'required|max:40',
        ]);
        
        // Create and save the new lab
        $newLab = new lab();
        $newLab->name = $request->lab_name;
        $newLab->phone = $request->lab_phone;
        $newLab->address = $request->lab_address;
        $newLab->save();

        // Return with success message
        return back()->with('success', 'Lab has been successfully created');
    }
    
    /**
     * Show the form for editing an existing external lab
     *
     * @param int $id The ID of the lab to edit
     * @return \Illuminate\View\View The lab edit form view
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If lab not found
     */
    public function returnUpdate($id)
    {
        // Find the lab by ID or fail with 404
        $lab = lab::findOrFail($id);
        
        // Return the edit view with lab data
        return view('labs.edit', compact('lab'));
    }
    
    /**
     * Update the specified external lab in the database
     *
     * This method retrieves the lab by ID, updates its properties with
     * the submitted form data, and saves the changes.
     *
     * @param Request $request The HTTP request containing updated lab data
     * @return \Illuminate\Http\RedirectResponse Redirect back with status message
     */
    public function update(Request $request)
    {
        // Find the lab by ID
        $lab = lab::where('id', $request->lab_id)->first();
        
        // Return error if lab not found
        if (!$lab) {
            return back()->with('error', 'Lab Not found');
        }
        
        // Update lab properties
        $lab->name = $request->lab_name;
        $lab->phone = $request->lab_phone;
        $lab->address = $request->lab_address;
        $lab->save();
        
        // Return with success message
        return back()->with('success', 'Lab has been successfully updated');
    }
}
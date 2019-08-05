<?php


namespace App\Http\Controllers\API;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Book;
use Illuminate\Support\Facades\Storage;
use  Validator ;


class BookController extends BaseController
{

public function index()
{
    $books = Book::all();
    return $this->sendResponse($books->toArray(), 'Books read succesfully');
}


public function store(Request $request)
{
    $input = $request->all();
    $validator =    Validator::make($input, [
    'name'=> 'required',
    'details'=> 'required'
    ] );

    if ($validator -> fails()) {
        return $this->sendError('error validation', $validator->errors());
    }



    if($request->hasFile('photo')) { //check file is getting or not..
        $file = $request->file('photo');
        $filename=uniqid('_'    ).".".$file->getClientOriginalExtension(); //create unique file name...
        Storage::disk('public')->put($filename,File::get($file));
        if(Storage::disk('public')->exists($filename)) {  // check file exists in directory or not
            $path = $request->file('photo')->move(public_path("/",$filename));
        }else {
            $photo_url = url('/',$filename);
        }
    }


    $book = Book::create($input);
    return $this->sendResponse($book->toArray(), 'Book  created succesfully');

}




public function show(  $id)
{
    $book = Book::find($id);
    if (   is_null($book)   ) {
        return $this->sendError(  'book not found ! ');
    }

    return $this->sendResponse($book->toArray(), 'Book read succesfully');

}



// update book
public function update(Request $request , Book $book)
{
    $input = $request->all();
    $validator =    Validator::make($input, [
    'name'=> 'required',
    'details'=> 'required'
    ] );

    if ($validator -> fails()) {
        ########...
        return $this->sendError('error validation', $validator->errors());
    }
    $book->name =  $input['name'];
    $book->details =  $input['details'];
    $book->photo =  $input['photo'];

    $book->save();
    return $this->sendResponse($book->toArray(), 'Book  updated succesfully');

}





// delete book
public function destroy(Book $book)
{

    $book->delete();

    return $this->sendResponse($book->toArray(), 'Book  deleted succesfully');

}




}


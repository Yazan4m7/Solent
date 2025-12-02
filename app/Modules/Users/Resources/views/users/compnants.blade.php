<!-- resources/views/page.blade.php -->
@extends('layouts.app') <!-- Use the layout -->
@section('header')
    <header>
        <h1>Welcome to My Website</h1>
    </header>
@show  <!-- @show displays the section's content -->

@section('content')
    <div>
        <h2>Main Content of the Page</h2>
        <p>This is the main content.</p>
    </div>
@show

@section('footer')
    <footer>
        <p>&copy; 2024 My Website</p>
    </footer>
@show

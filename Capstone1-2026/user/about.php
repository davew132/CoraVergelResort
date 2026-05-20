<?php
session_start(); 
require "../config/conn.php";
require "../config/security.php";
require "../config/auth.php";
requireUser();

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}
$user_id   = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'];
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CoraVergel Resort</title>
    <link rel="icon" href="../assets/images/cv_logo.png" >
    <link rel="stylesheet" href="../assets/css/index.css">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">


</head>
<body id="home">

<!-- TOPBAR -->
<div class="topbar">
    <div class="topbar-left">
        <div class="topbar-lang">
            <i class="fa-solid fa-globe"></i>
            <select onchange="changeLanguage(this.value)" aria-label="Select Language">
                <option value="en" selected>English</option>
                <option value="fil">Filipino</option>
            </select>
        </div>
    </div>
    <div class="topbar-right">
        <a href="https://www.google.com/maps/@10.714106,122.396162,16z" target="_blank" rel="noopener noreferrer" class="topbar-link">
            <i class="fa-solid fa-location-dot"></i>
        </a>
        <span class="topbar-divider">|</span>
        <a href="mailto:coravergelresort@gmail.com" class="topbar-link">
            <i class="fa-regular fa-envelope"></i>
        </a>
    </div>
</div>

<!-- NAVBAR -->
<nav class="navbar">
    <a href="#home" class="navbar-brand" onclick="smoothScroll(event, 'home')">
        <div class="navbar-logo-wrap">
            <img src="../assets/images/cv_logo.png" alt="CoraVergel Resort" class="navbar-logo-img">
            <span class="navbar-logo-ring"></span>
        </div>
        <div class="brand-text">
            <span class="brand-name">CoraVergel Resort</span>
            <span class="brand-sub">Paradise Awaits</span>
        </div>
    </a>
    <div class="nav-links">
        <a href="about.php">About</a>
        <a href="../user/rooms.php">Rooms &amp; Rates</a>
        <a href="#booking-section" onclick="smoothScroll(event, 'booking-section')">Booking</a>
        <a href="#gallery" onclick="smoothScroll(event, 'gallery')">Gallery</a>
        <a href="special_offers.php">Special Offers</a>
        <a href="reviews.php">Reviews</a>
        <a href="#contact" onclick="smoothScroll(event, 'contact')">Contact</a>
    </div>
    <div class="nav-login">
        <div class="profile-dropdown">
            <button class="profile-btn" onclick="toggleDropdown()">
                <i class="fa-regular fa-user"></i> <?= htmlspecialchars($full_name) ?>
                <i class="fa-solid" style="font-size:0.7rem; margin-left:4px;"></i>
            </button>
            <div class="dropdown-menu" id="dropdownMenu">
                <a href="profile.php"><i class="fa-regular fa-user"></i> Profile</a>
                <a href="profile.php"><i class="fa-regular fa-calendar"></i> Bookings</a>
                <div class="dropdown-divider"></div>
                <a href="../user/logout.php"><i class="fa-solid fa-right-from-bracket" style="color:#e53e3e;"></i><span style="color:#e53e3e;"> Logout</span></a>
            </div>
        </div>
    </div>
</nav>

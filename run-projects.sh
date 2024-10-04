#!/bin/zsh

# Start PHP server
php -S localhost:8000 -t ./backend &

# Start NextJS development server
npm --prefix ./frontend run dev

# Wait for both processes
wait
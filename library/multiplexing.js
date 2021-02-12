function openDatabase(database){
    setCookie('Database', database);
}

function closeDatabase(){
    delCookie('Database');
}
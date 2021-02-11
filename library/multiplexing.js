function openDatabase(database, open_success = null, open_fail = null){
    setCookie('Database', database);
    if (open_success || open_fail){

    }
}
function closeDatabase(){
    delCookie('Database');
}
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC

def realizaLogin():
    driver = webdriver.Chrome()
    driver.get("http://localhost:8080/ALEXANDRIA_PHP/template/index.php")
    
    driver.find_element(By.ID, "usuario").send_keys('joao.silva')
    driver.find_element(By.ID, "senha").send_keys('Password123')
    driver.find_element(By.ID, "login_btn").click()
    
    WebDriverWait(driver, 10).until(
        EC.url_contains("home.php")
    )
    
    return driver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
import time
from login import realizaLogin

try:
    driver = realizaLogin()
    
    driver.get("http://localhost:8080/ALEXANDRIA_PHP/template/gestao/remessa-gestao.php")
    
    # ABRINDO POPUP CADASTRO
    try:
        time.sleep(5)
        driver.find_element(By.ID, "btn_abrePopupCadastro").click()
    except:
        print("Popup já aberto ou não necessário")
    
    time.sleep(5)
    
    # PREENCHENDO FORMULÁRIO CADASTRO
    driver.find_element(By.ID, "cad_forn").send_keys("34.567.890/0001-03")
    driver.find_element(By.ID, "cad_livro").send_keys("978-85-325-0264-2")
    driver.find_element(By.ID, "cad_qtd").send_keys("25")
    
    submit_button = WebDriverWait(driver, 10).until(
        EC.element_to_be_clickable((By.ID, "cad_btn"))
    )
    submit_button.click()
    
    # CADASTRO BEM SUCEDIDO?
    try:
        WebDriverWait(driver, 10).until(
            EC.presence_of_element_located((By.CLASS_NAME, "alert-success"))  # Ajuste para o elemento de sucesso do seu sistema
        )
        print("Cadastro realizado com sucesso!")
    except:
        print("Possível falha no cadastro")
    
    time.sleep(5)
    
finally:
    driver.quit()
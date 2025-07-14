from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
import time
from login import realizaLogin, definirUrl

try:
    driver = realizaLogin()
    url = definirUrl()
    
    driver.get(url + "gestao/fornecedor-gestao.php")
    
    # ABRINDO POPUP CADASTRO
    try:
        time.sleep(5)
        driver.find_element(By.ID, "btn_abrePopupCadastro").click()
    except:
        print("Popup já aberto ou não necessário")
    
    time.sleep(5)
    
    # PREENCHENDO FORMULÁRIO CADASTRO
    driver.find_element(By.ID, "cad_nome").send_keys("Editora Arco-Íris")
    driver.find_element(By.ID, "cad_cnpj").send_keys("00.000.000/0000-00")
    driver.find_element(By.ID, "cad_telefone").send_keys("(00) 00000-0000")
    driver.find_element(By.ID, "cad_email").send_keys("editora_arcoiris@gmail.com")
    driver.find_element(By.ID, "cad_endereco").send_keys("Rua das Flores, 657 - SP")
    
    submit_button = WebDriverWait(driver, 10).until(
        EC.element_to_be_clickable((By.ID, "cad_btn"))
    )
    submit_button.click()
    time.sleep(5)
    
finally:
    driver.quit()
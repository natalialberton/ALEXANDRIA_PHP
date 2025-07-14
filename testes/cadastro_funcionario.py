from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
import time
from login import realizaLogin, definirUrl

try:
    driver = realizaLogin()
    url = definirUrl()
    
    driver.get(url + "gestao/funcionario-gestao.php")
    
    # ABRINDO POPUP CADASTRO
    try:
        time.sleep(5)
        driver.find_element(By.ID, "btn_abrePopupCadastro").click()
    except:
        print("Popup já aberto ou não necessário")
    
    time.sleep(5)

    # PREENCHENDO FORMULÁRIO CADASTRO
    driver.find_element(By.ID, "cad_nome").send_keys("Marcos Oliveira")
    driver.find_element(By.ID, "cad_cpf").send_keys("000.000.000-00")
    driver.find_element(By.ID, "cad_telefone").send_keys("(00) 00000-0000")
    driver.find_element(By.ID, "cad_email").send_keys("oliveira_marcos@gmail.com")
    driver.find_element(By.ID, "cad_login").send_keys("marcos.oliveira")
    driver.find_element(By.ID, "cad_senha").send_keys("PdfE3245")
    
    submit_button = WebDriverWait(driver, 10).until(
        EC.element_to_be_clickable((By.ID, "cad_btn"))
    )
    submit_button.click()
    time.sleep(5)
    
finally:
    driver.quit()
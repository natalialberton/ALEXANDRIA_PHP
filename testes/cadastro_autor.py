from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
import time
from login import realizaLogin, definirUrl

try:
    driver = realizaLogin()
    url = definirUrl()
    
    driver.get(url + "gestao/autor-gestao.php")
    
    # ABRINDO POPUP CADASTRO
    try:
        time.sleep(5)
        driver.find_element(By.ID, "btn_abrePopupCadastro").click()
    except:
        print("Popup já aberto ou não necessário")
    
    time.sleep(5)
    
    # PREENCHENDO FORMULÁRIO CADASTRO
    driver.find_element(By.ID, "cad_nome").send_keys("R F Kuang")
    driver.find_element(By.ID, "cad_dataNascimento").send_keys("05081998")
    driver.find_element(By.ID, "cad_categoria").send_keys("Fantasia")
    
    submit_button = WebDriverWait(driver, 10).until(
        EC.element_to_be_clickable((By.ID, "cad_btn"))
    )
    submit_button.click()
    time.sleep(5)
    
finally:
    driver.quit()
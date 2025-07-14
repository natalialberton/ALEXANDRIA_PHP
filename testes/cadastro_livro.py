from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
import time
from login import realizaLogin, definirUrl

try:
    driver = realizaLogin()
    url = definirUrl()
    
    driver.get(url + "gestao/livro-gestao.php")
    
    # ABRINDO POPUP CADASTRO
    try:
        time.sleep(5)
        driver.find_element(By.ID, "btn_abrePopupCadastro").click()
    except:
        print("Popup já aberto ou não necessário")
    
    time.sleep(5)
    
    # PRENCHENDO FORMULÁRIO CADASTRO
    driver.find_element(By.ID, "cad_titulo").send_keys("Quincas Borba")
    driver.find_element(By.ID, "cad_isbn").send_keys("343-33-333-3373-3")
    driver.find_element(By.ID, "cad_autor").send_keys("Machado de Assis")
    driver.find_element(By.ID, "cad_categoria").send_keys("Literatura Brasileira")
    driver.find_element(By.ID, "cad_edicao").send_keys("1")
    driver.find_element(By.ID, "cad_anoPublicacao").send_keys("1988")
    driver.find_element(By.ID, "cad_nPaginas").send_keys("234")
    driver.find_element(By.ID, "cad_idioma").send_keys("Português")
    driver.find_element(By.ID, "cad_estoque").send_keys("30")
    driver.find_element(By.ID, "cad_sinopse").send_keys("O livro trata de um cachorro louco, tal como seu dono.")
    
    submit_button = WebDriverWait(driver, 10).until(
        EC.element_to_be_clickable((By.ID, "cad_btn"))
    )
    submit_button.click()
    time.sleep(5)
    
finally:
    driver.quit()
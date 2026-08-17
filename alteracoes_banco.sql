/*UPDATE empresas
SET regime_tributario = CASE 
    WHEN is_optante_simples_nac = 1 THEN 'simples'
    ELSE 'normal'
END,
op_simp_nac = CASE 
    WHEN is_optante_simples_nac = 1 THEN 3
    ELSE 1
END*/


